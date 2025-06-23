<?php

namespace Database\Seeders;

use App\Models\ProductVariation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductVariation::where('color', 'azul')->update(['color' => '#0000FF']);
        ProductVariation::where('color', 'rosa')->update(['color' => '#FFC0CB']);
        ProductVariation::where('color', 'azul bebe')->update(['color' => '#ADD8E6']);
        ProductVariation::where('color', 'preto')->update(['color' => '#000000']);
        ProductVariation::where('color', 'cinza')->update(['color' => '#808080']);
        ProductVariation::where('color', 'azul marinho')->update(['color' => '#000080']);
        ProductVariation::where('color', 'vermelho')->update(['color' => '#FF0000']);
        ProductVariation::where('color', 'verde')->update(['color' => '#008000']);
        ProductVariation::where('color', 'vinho')->update(['color' => '#800000']);
        ProductVariation::where('color', 'rosa pink')->update(['color' => '#FF69B4']);
        ProductVariation::where('color', 'rosa pink e branco')->update(['color' => '#FF69B4']);
        ProductVariation::where('color', 'marrom')->update(['color' => '#A52A2A']);
        ProductVariation::where('color', 'nude')->update(['color' => '#F5CBA7']);
        ProductVariation::where('color', 'preto e branco')->update(['color' => '#000000']);
    }
}
