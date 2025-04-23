<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function($query) {
            $query->where('active', true)->with('images');
        }])
            ->get();

        return view('home', compact('categories'));
    }
}
