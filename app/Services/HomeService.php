<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class HomeService
{
    public function getCategories(): Collection
    {
        return Category::with(['products' => function($query) {
            $query->where('active', true)
                ->with('images', 'variations')
                ->orderBy('id', 'desc');
        }])
            ->get();
    }
}
