<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Importar dados do CSV da planilha Leca
        $csvFile = storage_path('app/catalogo_fitness.csv');
        $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Pular cabeçalho
        array_shift($lines);
        array_shift($lines);

        $categoriesIds = [
            'fitness' => Category::where('slug', 'fitness')->first()->id,
            'pijamas' => Category::where('slug', 'pijamas')->first()->id,
        ];

        $productGroups = [];

        // Primeiro, agrupar produtos similares
        foreach ($lines as $line) {
            $data = str_getcsv($line);

            if (count($data) < 8 || empty($data[1])) {
                continue;
            }

            $code = $data[1];
            $categorySlug = $data[2];
            $subcategory = $data[3];
            $name = $data[4];
            $size = $data[5];
            $color = $data[6];
            $price = (float) str_replace(['$', ','], ['', '.'], $data[7]);

            // Agrupar por nome do produto para não criar duplicatas
            $productKey = $categorySlug . '-' . Str::slug($name);

            if (!isset($productGroups[$productKey])) {
                $productGroups[$productKey] = [
                    'code' => $code,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category_id' => $categoriesIds[$categorySlug],
                    'subcategory' => $subcategory,
                    'price' => $price,
                    'purchase_price' => $price,
                    'variations' => []
                ];
            }

            // Adicionar variação
            $productGroups[$productKey]['variations'][] = [
                'size' => $size,
                'color' => $color,
                'stock' => 10 // Default stock quantity
            ];
        }

        // Criar produtos e variações
        foreach ($productGroups as $product) {
            $variations = $product['variations'];
            unset($product['variations']);

            // Criar produto
            $newProduct = Product::create($product);

            // Criar variações
            foreach ($variations as $variation) {
                ProductVariation::create([
                    'product_id' => $newProduct->id,
                    'size' => $variation['size'],
                    'color' => $variation['color'],
                    'stock' => $variation['stock']
                ]);
            }
        }
    }
}
