<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductAdminController extends Controller
{
    /**
     * Check if user is authenticated and redirect if not
     */
    private function checkAuth()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->checkAuth();

        $products = Product::with('category', 'images')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $this->checkAuth();

        $categories = Category::all();
        $colors = $this->getColors();

        return view('admin.products.create', compact('categories', 'colors'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkAuth();

        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'sizes' => 'required|array|min:1',
            'colors' => 'nullable|array',
        ]);

        // Validar campos de estoque dinâmicos
        $this->validateStockFields($request);

        // Criar produto
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => $request->code,
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

        // Criar variações (combinações de tamanhos e cores)
        $this->createProductVariations($request, $product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $this->checkAuth();

        $product = Product::with(['variations', 'images'])->findOrFail($id);
        $categories = Category::all();

        // Obtém os tamanhos e cores únicas para este produto
        $sizes = $product->variations->pluck('size')->unique()->values();
        $selectedColors = $product->variations->whereNotNull('color')->pluck('color')->unique()->values();
        $colors = collect($this->getColors());

        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors', 'selectedColors'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkAuth();

        $product = Product::findOrFail($id);

        // Verificar se já existem variações
        $hasExistingVariations = $product->variations()->count() > 0;
        try {
        $request->validate([
            'name' => 'max:255',
            'code' => 'unique:products,code,' . $id,
            'price' => 'numeric|min:0',
            'purchase_price' => 'numeric|min:0',
            'category_id' => 'exists:categories,id',
            'subcategory' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'sizes' => $hasExistingVariations ? 'nullable|array' : 'array|min:1',
            'colors' => 'nullable|array',
        ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            Log::error($exception->errors());
            return back()->withErrors($exception->errors())->withInput();
        }

        // Validar campos de estoque dinâmicos
        $this->validateStockFields($request);

        // Atualizar produto
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->code = $request->code;
        $product->price = (float) $request->price;
        $product->purchase_price = (float) $request->purchase_price;
        $product->category_id = (int) $request->category_id;
        $product->subcategory = $request->subcategory;
        $product->description = $request->description;
        $product->active = $request->boolean('active');
        $product->save();

        // Processar upload de novas imagens
        if ($request->hasFile('images')) {
            $this->processImageUploads($request, $product, false);
        }

        // Atualizar variações
        $this->updateProductVariations($request, $product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->checkAuth();

        $product = Product::findOrFail($id);

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

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Set an image as the main image for a product.
     *
     * @param  int  $productId
     * @param  int  $imageId
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Delete an image from a product.
     *
     * @param  int  $productId
     * @param  int  $imageId
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Reorder images for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Validate stock fields dynamically
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function validateStockFields(Request $request)
    {
        $stockFields = collect($request->all())->filter(function($value, $key) {
            return str_starts_with($key, 'stock_');
        });

        foreach($stockFields as $key => $value) {
            if ($value !== null && (!is_numeric($value) || $value < 0)) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    [$key => ['Estoque deve ser um número positivo']]
                );
            }
        }
    }

    /**
     * Process image uploads for a product
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @param bool $isFirstImageMain
     * @return void
     */
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

    /**
     * Create product variations for a new product
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @return void
     */
    private function createProductVariations(Request $request, Product $product)
    {
        $sizes = $request->sizes;
        $colors = $request->colors ?? [];

        if (empty($colors)) {
            // Se não houver cores especificadas, criar variações apenas com tamanhos
            foreach ($sizes as $size) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => null,
                    'stock' => $request->input('stock_' . $size, 0),
                    'active' => true,
                ]);
            }
        } else {
            // Criar variações com tamanhos e cores
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color,
                        'stock' => $request->input('stock_' . $size . '_' . $color, 0),
                        'active' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Update product variations for an existing product
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @return void
     */
    private function updateProductVariations(Request $request, Product $product)
    {
        $sizes = $request->sizes ?? $product->variations->pluck('size')->unique()->toArray();
        $colors = $request->colors ?? [];

        // Obter variações existentes
        $existingVariations = $product->variations()->get()->keyBy(function($variation) {
            return $variation->size . '_' . ($variation->color ?? 'null');
        });

        $newVariations = collect();

        if (empty($colors)) {
            // Se não houver cores especificadas, criar/atualizar variações apenas com tamanhos
            foreach ($sizes as $size) {
                $key = $size . '_null';
                $stock = $request->input('stock_' . $size, 0);

                if ($existingVariations->has($key)) {
                    // Atualizar existente
                    $variation = $existingVariations->get($key);
                    $variation->update([
                        'stock' => $stock,
                        'active' => true,
                    ]);
                    $newVariations->push($key);
                } else {
                    // Criar nova
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => null,
                        'stock' => $stock,
                        'active' => true,
                    ]);
                    $newVariations->push($key);
                }
            }
        } else {
            // Criar/atualizar variações com tamanhos e cores
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $key = $size . '_' . $color;
                    $stock = $request->input('stock_' . $size . '_' . $color, 0);

                    if ($existingVariations->has($key)) {
                        // Atualizar existente
                        $variation = $existingVariations->get($key);
                        $variation->update([
                            'stock' => $stock,
                            'active' => true,
                        ]);
                        $newVariations->push($key);
                    } else {
                        // Criar nova
                        ProductVariation::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'color' => $color,
                            'stock' => $stock,
                            'active' => true,
                        ]);
                        $newVariations->push($key);
                    }
                }
            }
        }

        // Remover variações que não estão mais sendo usadas
        $variationsToDelete = $existingVariations->keys()->diff($newVariations);
        foreach ($variationsToDelete as $keyToDelete) {
            $existingVariations->get($keyToDelete)->delete();
        }
    }

    /**
     * Get available colors array
     *
     * @return array
     */
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
