<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fitness',
                'slug' => 'fitness',
                'description' => 'Produtos de moda fitness para prática esportiva e bem-estar.'
            ],
            [
                'name' => 'Pijamas',
                'slug' => 'pijamas',
                'description' => 'Pijamas confortáveis para toda a família.'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
