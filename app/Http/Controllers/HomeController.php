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
        return view('home', compact('categories'));
    }
}
