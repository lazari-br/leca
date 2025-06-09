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
        ProductVariation::where('color', 'Azul')->update(['color' => '#0000FF']);
        ProductVariation::where('color', 'Rosa')->update(['color' => '#FFC0CB']);
        ProductVariation::where('color', 'Azul bebe')->update(['color' => '#ADD8E6']);
        ProductVariation::where('color', 'Preto')->update(['color' => '#000000']);
        ProductVariation::where('color', 'Cinza')->update(['color' => '#808080']);
        ProductVariation::where('color', 'Azul marinho')->update(['color' => '#000080']);
        ProductVariation::where('color', 'Vermelho')->update(['color' => '#FF0000']);
        ProductVariation::where('color', 'Verde')->update(['color' => '#008000']);
        ProductVariation::where('color', 'Vinho')->update(['color' => '#800000']);
        ProductVariation::where('color', 'Rosa pink')->update(['color' => '#FF69B4']);
        ProductVariation::where('color', 'Rosa pink e branco')->update(['color' => '#FF69B4']);
        ProductVariation::where('color', 'Marrom')->update(['color' => '#A52A2A']);
        ProductVariation::where('color', 'Nude')->update(['color' => '#F5CBA7']);
        ProductVariation::where('color', 'Preto e branco')->update(['color' => '#000000']);
    }
}
