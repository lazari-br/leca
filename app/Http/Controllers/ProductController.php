<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['variations', 'images'])
            ->firstOrFail();

        // Agrupando variações por tamanho e cor
        $sizes = $product->variations->pluck('size')->unique()->values();
        $colors = $product->variations->pluck('color')->unique()->values()->filter();

        return view('products.show', compact('product', 'sizes', 'colors'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->where('active', true)
            ->with(['variations', 'images'])
            ->paginate(12);

        return view('products.category', compact('category', 'products'));
    }

    public function subcategory($categorySlug, $subcategory)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->where('subcategory', $subcategory)
            ->where('active', true)
            ->with(['variations', 'images'])
            ->paginate(12);

        return view('products.subcategory', compact('category', 'subcategory', 'products'));
    }

    public function getVariationDetails(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'color' => 'nullable|string',
        ]);

        $variation = \App\Models\ProductVariation::where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if (!$variation) {
            return response()->json(['error' => 'Variação não encontrada'], 404);
        }

        return response()->json([
            'id' => $variation->id,
            'code' => $variation->code,
            'stock' => $variation->stock,
            'available' => $variation->stock > 0,
        ]);
    }
}
