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
            ->with('variations')
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
            ->paginate(12);

        return view('products.category', compact('category', 'products'));
    }

    public function subcategory($categorySlug, $subcategory)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $products = Product::where('category_id', $category->id)
            ->where('subcategory', $subcategory)
            ->where('active', true)
            ->paginate(12);

        return view('products.subcategory', compact('category', 'subcategory', 'products'));
    }
}
