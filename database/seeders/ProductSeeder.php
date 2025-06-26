<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $this->refreshBase();

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
            $data = explode(',', $line);

            if (count($data) < 8 || empty($data[1])) {
                continue;
            }

            $code = $data[0];
            $categorySlug = $data[1];
            $subcategory = $data[2];
            $name = ucfirst($data[3]);
            $size = $data[4];
            $color = $data[5];
            $price = (float) str_replace(['$', ','], ['', '.'], $data[6]);
            $purchasePrice = (float) str_replace(['$', ','], ['', '.'], $data[7]);

            // Agrupar por nome do produto para não criar duplicatas
            $productKey = $categorySlug . '-' . Str::slug($name);

            if (!isset($productGroups[$productKey])) {
                $productGroups[$productKey] = [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category_id' => $categoriesIds[$categorySlug],
                    'subcategory' => $subcategory,
                    'price' => $price,
                    'purchase_price' => $purchasePrice,
                    'variations' => []
                ];
            }

            // Adicionar variação
            $productGroups[$productKey]['variations'][] = [
                'code' => $code,
                'size' => $size,
                'color' => $color,
                'stock' => 1 // Default stock quantity
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
                    'code' => $variation['code'],
                    'size' => $variation['size'],
                    'color' => $variation['color'],
                    'stock' => $variation['stock']
                ]);
            }
        }
    }

    private function refreshBase(): void
    {
        DB::select("SET FOREIGN_KEY_CHECKS = 0;");
        DB::select("truncate products;");
        DB::select("truncate product_variations;");
        DB::select("truncate purchases;");
        DB::select("truncate purchase_items;");
        DB::select("truncate sales;");
        DB::select("truncate sale_items;");
        DB::select("SET FOREIGN_KEY_CHECKS = 0;");
    }
}
