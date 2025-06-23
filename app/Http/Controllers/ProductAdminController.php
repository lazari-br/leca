<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductAdminController extends Controller
{
    private function checkAuth()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function index(): ?View
    {
        $this->checkAuth();
        $products = Product::with('category', 'images', 'variations')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create(): ?View
    {
        $this->checkAuth();
        $categories = Category::all();
        $colors = $this->getColors();
        return view('admin.products.create', compact('categories', 'colors'));
    }

    public function store(Request $request)
    {
        $this->checkAuth();

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'skus' => 'required|array|min:1',
            'skus.*.code' => 'required|string|max:255|unique:product_variations,code',
            'skus.*.size' => 'required|string|max:50',
            'skus.*.color' => 'nullable|string|max:50',
            'skus.*.stock' => 'required|integer|min:0',
        ], [
            'skus.required' => 'É necessário adicionar pelo menos um SKU.',
            'skus.*.code.required' => 'O código do SKU é obrigatório.',
            'skus.*.code.unique' => 'Este código de SKU já existe.',
            'skus.*.size.required' => 'O tamanho é obrigatório.',
            'skus.*.stock.required' => 'O estoque é obrigatório.',
        ]);

        DB::transaction(function () use ($request) {
            // Criar produto
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'price' => (float) $request->price,
                'purchase_price' => (float) $request->purchase_price,
                'category_id' => (int) $request->category_id,
                'subcategory' => $request->subcategory,
                'description' => $request->description,
                'active' => $request->boolean('active'),
            ]);

            // Processar upload de imagens
            if ($request->hasFile('images')) {
                $this->processImageUploads($request, $product);
            }

            // Criar variações (SKUs)
            foreach ($request->skus as $skuData) {
                // Converter nome da cor para hex se necessário
                $colorValue = $skuData['color'] ?: null;
                if ($colorValue) {
                    $colorMap = [
                        'Preto' => '#000000',
                        'Branco' => '#FFFFFF',
                        'Cinza' => '#808080',
                        'Vermelho' => '#FF0000',
                        'Rosa' => '#FFC0CB',
                        'Azul' => '#0000FF',
                        'Verde' => '#008000',
                        'Amarelo' => '#FFFF00',
                        'Roxo' => '#800080',
                        'Laranja' => '#FFA500',
                    ];

                    if (isset($colorMap[$colorValue])) {
                        $colorValue = $colorMap[$colorValue];
                    }
                }

                ProductVariation::create([
                    'product_id' => $product->id,
                    'code' => $skuData['code'],
                    'size' => strtolower($skuData['size']), // Salvar em minúsculas
                    'color' => $colorValue,
                    'stock' => (int) $skuData['stock'],
                    'active' => true,
                ]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function edit($id): ?View
    {
        $this->checkAuth();

        $product = Product::with(['variations', 'images'])->findOrFail($id);
        $categories = Category::all();
        $colors = $this->getColors();

        return view('admin.products.edit', compact('product', 'categories', 'colors'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAuth();

        $product = Product::findOrFail($id);

        // Validação customizada para códigos únicos
        $this->validateUniqueSkuCodes($request, $id);

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'skus' => 'nullable|array',
            'skus.*.code' => 'required_with:skus|string|max:255',
            'skus.*.size' => 'required_with:skus|string|max:50',
            'skus.*.color' => 'nullable|string|max:50',
            'skus.*.stock' => 'required_with:skus|integer|min:0',
            'update_variations' => 'nullable|array',
            'update_variations.*.id' => 'required_with:update_variations|exists:product_variations,id',
            'update_variations.*.code' => 'required_with:update_variations|string|max:255',
            'update_variations.*.size' => 'required_with:update_variations|string|max:50',
            'update_variations.*.color' => 'nullable|string|max:50',
            'update_variations.*.stock' => 'required_with:update_variations|integer|min:0',
        ], [
            'skus.*.code.required_with' => 'O código do SKU é obrigatório.',
            'skus.*.size.required_with' => 'O tamanho é obrigatório.',
            'skus.*.stock.required_with' => 'O estoque é obrigatório.',
            'update_variations.*.code.required_with' => 'O código do SKU é obrigatório.',
            'update_variations.*.size.required_with' => 'O tamanho é obrigatório.',
            'update_variations.*.stock.required_with' => 'O estoque é obrigatório.',
        ]);

        DB::transaction(function () use ($request, $product) {
            // Atualizar produto
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'price' => (float) $request->price,
                'purchase_price' => (float) $request->purchase_price,
                'category_id' => (int) $request->category_id,
                'subcategory' => $request->subcategory,
                'description' => $request->description,
                'active' => $request->boolean('active'),
            ]);

            // Processar upload de novas imagens
            if ($request->hasFile('images')) {
                $this->processImageUploads($request, $product, false);
            }

            // Atualizar variações existentes
            if ($request->has('update_variations') && is_array($request->update_variations)) {
                foreach ($request->update_variations as $variationData) {
                    // Validar se todos os campos necessários estão presentes
                    if (!isset($variationData['id']) || !isset($variationData['code']) ||
                        !isset($variationData['size']) || !isset($variationData['stock'])) {
                        continue;
                    }

                    $variation = ProductVariation::where('id', $variationData['id'])
                        ->where('product_id', $product->id)
                        ->first();

                    if ($variation) {
                        // Converter nome da cor para hex se necessário
                        $colorValue = isset($variationData['color']) ? $variationData['color'] : null;
                        if ($colorValue && $colorValue !== '') {
                            $colorValue = $this->convertColorNameToHex($colorValue);
                        }

                        // Log para debug
                        Log::info('Atualizando variação', [
                            'variation_id' => $variation->id,
                            'old_stock' => $variation->stock,
                            'new_stock' => $variationData['stock'],
                            'variation_data' => $variationData
                        ]);

                        $variation->update([
                            'code' => $variationData['code'],
                            'size' => strtolower($variationData['size']),
                            'color' => $colorValue,
                            'stock' => (int) $variationData['stock'],
                        ]);

                        // Verificar se a atualização funcionou
                        $variation->refresh();
                        Log::info('Variação atualizada', [
                            'variation_id' => $variation->id,
                            'updated_stock' => $variation->stock
                        ]);
                    }
                }
            }

            // Adicionar novas variações
            if ($request->has('skus') && is_array($request->skus)) {
                foreach ($request->skus as $skuData) {
                    // Converter nome da cor para hex se necessário
                    $colorValue = isset($skuData['color']) ? $skuData['color'] : null;
                    if ($colorValue && $colorValue !== '') {
                        $colorValue = $this->convertColorNameToHex($colorValue);
                    }

                    ProductVariation::create([
                        'product_id' => $product->id,
                        'code' => $skuData['code'],
                        'size' => strtolower($skuData['size']),
                        'color' => $colorValue,
                        'stock' => (int) $skuData['stock'],
                        'active' => true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Validar códigos únicos de SKU
     */
    private function validateUniqueSkuCodes(Request $request, $productId)
    {
        $allCodes = [];
        $errors = [];

        // Coletar códigos das variações existentes
        if ($request->has('update_variations') && is_array($request->update_variations)) {
            foreach ($request->update_variations as $index => $variation) {
                if (isset($variation['code'])) {
                    $code = $variation['code'];

                    // Verificar se o código já existe em outro produto
                    $existingVariation = ProductVariation::where('code', $code)
                        ->where('product_id', '!=', $productId)
                        ->first();

                    if ($existingVariation) {
                        $errors["update_variations.{$index}.code"] = "O código '{$code}' já existe em outro produto.";
                    }

                    // Verificar duplicatas na própria requisição
                    if (in_array($code, $allCodes)) {
                        $errors["update_variations.{$index}.code"] = "O código '{$code}' está duplicado.";
                    } else {
                        $allCodes[] = $code;
                    }
                }
            }
        }

        // Coletar códigos dos novos SKUs
        if ($request->has('skus') && is_array($request->skus)) {
            foreach ($request->skus as $index => $sku) {
                if (isset($sku['code'])) {
                    $code = $sku['code'];

                    // Verificar se o código já existe
                    $existingVariation = ProductVariation::where('code', $code)->first();

                    if ($existingVariation) {
                        $errors["skus.{$index}.code"] = "O código '{$code}' já existe.";
                    }

                    // Verificar duplicatas na própria requisição
                    if (in_array($code, $allCodes)) {
                        $errors["skus.{$index}.code"] = "O código '{$code}' está duplicado.";
                    } else {
                        $allCodes[] = $code;
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    /**
     * Converter nome da cor para hex
     */
    private function convertColorNameToHex($colorName)
    {
        $colorMap = [
            'Preto' => '#000000',
            'Branco' => '#FFFFFF',
            'Cinza' => '#808080',
            'Vermelho' => '#FF0000',
            'Rosa' => '#FFC0CB',
            'Azul' => '#0000FF',
            'Verde' => '#008000',
            'Amarelo' => '#FFFF00',
            'Roxo' => '#800080',
            'Laranja' => '#FFA500',
        ];

        return isset($colorMap[$colorName]) ? $colorMap[$colorName] : $colorName;
    }

    public function destroy($id)
    {
        $this->checkAuth();

        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            // Remover imagens
            foreach ($product->images as $image) {
                if (Storage::exists(str_replace('storage/', 'public/', $image->image_path))) {
                    Storage::delete(str_replace('storage/', 'public/', $image->image_path));
                }
                $image->delete();
            }

            // Remover imagem legada se existir
            if ($product->image && Storage::exists(str_replace('storage/', 'public/', $product->image))) {
                Storage::delete(str_replace('storage/', 'public/', $product->image));
            }

            // Remover variações
            $product->variations()->delete();

            // Remover produto
            $product->delete();
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function deleteVariation($productId, $variationId)
    {
        $this->checkAuth();

        $variation = ProductVariation::where('product_id', $productId)
            ->where('id', $variationId)
            ->firstOrFail();

        $variation->delete();

        return redirect()->route('admin.products.edit', $productId)
            ->with('success', 'SKU excluído com sucesso!');
    }

    public function setMainImage($productId, $imageId)
    {
        $this->checkAuth();

        $product = Product::findOrFail($productId);

        // Remover a marcação principal de todas as imagens do produto
        $product->images()->update(['is_main' => false]);

        // Definir a imagem selecionada como principal
        $image = ProductImage::where('product_id', $productId)
            ->where('id', $imageId)
            ->firstOrFail();

        $image->is_main = true;
        $image->save();

        return redirect()->route('admin.products.edit', $productId)
            ->with('success', 'Imagem principal definida com sucesso!');
    }

    public function deleteImage($productId, $imageId)
    {
        $this->checkAuth();

        $image = ProductImage::where('product_id', $productId)
            ->where('id', $imageId)
            ->firstOrFail();

        // Excluir arquivo
        if (Storage::exists(str_replace('storage/', 'public/', $image->image_path))) {
            Storage::delete(str_replace('storage/', 'public/', $image->image_path));
        }

        // Se esta era a imagem principal, definir outra como principal
        if ($image->is_main) {
            $nextImage = ProductImage::where('product_id', $productId)
                ->where('id', '!=', $imageId)
                ->first();

            if ($nextImage) {
                $nextImage->is_main = true;
                $nextImage->save();
            }
        }

        // Excluir registro
        $image->delete();

        return redirect()->route('admin.products.edit', $productId)
            ->with('success', 'Imagem excluída com sucesso!');
    }

    public function reorderImages(Request $request, $productId)
    {
        $this->checkAuth();

        $request->validate([
            'imageIds' => 'required|array',
            'imageIds.*' => 'required|integer|exists:product_images,id'
        ]);

        $imageIds = $request->imageIds;

        foreach ($imageIds as $order => $imageId) {
            ProductImage::where('id', $imageId)
                ->where('product_id', $productId)
                ->update(['order' => $order]);
        }

        return response()->json(['success' => true]);
    }

    private function processImageUploads(Request $request, Product $product, bool $isFirstImageMain = true)
    {
        $isFirstImage = $isFirstImageMain && $product->images()->count() === 0;
        $maxOrder = $product->images()->max('order') ?? -1;

        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $fileName = time() . '_' . Str::random(5) . '_' . $image->getClientOriginalName();
                $imagePath = Storage::putFileAs('product_images', $image, $fileName, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_main' => $isFirstImage,
                    'order' => $maxOrder + 1
                ]);

                $isFirstImage = false;
                $maxOrder++;
            }
        }
    }

    private function getColors(): array
    {
        return [
            [
                'name' => 'Preto',
                'hex' => '#000000'
            ], [
                'name' => 'Branco',
                'hex' => '#FFFFFF'
            ], [
                'name' => 'Cinza',
                'hex' => '#808080'
            ], [
                'name' => 'Vermelho',
                'hex' => '#FF0000'
            ], [
                'name' => 'Rosa',
                'hex' => '#FFC0CB'
            ], [
                'name' => 'Azul',
                'hex' => '#0000FF'
            ], [
                'name' => 'Verde',
                'hex' => '#008000'
            ], [
                'name' => 'Amarelo',
                'hex' => '#FFFF00'
            ], [
                'name' => 'Roxo',
                'hex' => '#800080'
            ], [
                'name' => 'Laranja',
                'hex' => '#FFA500'
            ],
        ];
    }
}
