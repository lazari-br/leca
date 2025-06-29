<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\HomeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(public HomeService $homeService) {}

    public function index(): ?View
    {
        $categories = $this->homeService->getCategories();
        $featuredProducts = Product::where('active', true)
            ->whereHas('category', function($query) {
                $query->where('slug', 'fitness');
            })
            ->with(['variations', 'images', 'category'])
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }
}
