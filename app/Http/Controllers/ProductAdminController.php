<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware para garantir que apenas usuários autenticados acessem estas rotas
        $this->middleware('auth');
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
            'active' => 'boolean',
            'sizes' => 'required|array|min:1',
            'colors' => 'nullable|array',
        ]);

        // Processar upload de imagem
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Criar produto
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => $request->code,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'subcategory' => $request->subcategory,
            'description' => $request->description,
            'image' => $imagePath ? 'storage/' . $imagePath : null,
            'active' => $request->has('active'),
        ]);

        // Criar variações (combinações de tamanhos e cores)
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

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with('variations')->findOrFail($id);
        $categories = Category::all();
        
        // Obtém os tamanhos e cores únicas para este produto
        $sizes = $product->variations->pluck('size')->unique()->values();
        $colors = $product->variations->pluck('color')->unique()->filter()->values();
        
        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'colors'));
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
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products,code,' . $id,
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
            'active' => 'boolean',
            'sizes' => 'required|array|min:1',
            'colors' => 'nullable|array',
        ]);

        // Processar upload de imagem
        if ($request->hasFile('image')) {
            // Remover imagem antiga se existir
            if ($product->image && Storage::exists(str_replace('storage/', 'public/', $product->image))) {
                Storage::delete(str_replace('storage/', 'public/', $product->image));
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = 'storage/' . $imagePath;
        }

        // Atualizar produto
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->code = $request->code;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->subcategory = $request->subcategory;
        $product->description = $request->description;
        $product->active = $request->has('active');
        $product->save();

        // Atualizar variações
        $sizes = $request->sizes;
        $colors = $request->colors ?? [];
        
        // Remover variações antigas
        $product->variations()->delete();
        
        // Criar novas variações
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
        $product = Product::findOrFail($id);
        
        // Remover imagem se existir
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
}