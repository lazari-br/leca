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
        ProductVariation::where(['color'=>'Laranja'])->update(['color'=>'#FFA500']);
        ProductVariation::where(['color'=>'Roxo'])->update(['color'=>'#800080']);
        ProductVariation::where(['color'=>'Amarelo'])->update(['color'=>'#FFFF00']);
        ProductVariation::where(['color'=>'Verde'])->update(['color'=>'#008000']);
        ProductVariation::where(['color'=>'Azul'])->update(['color'=>'#0000FF']);
        ProductVariation::where(['color'=>'Rosa'])->update(['color'=>'#FFC0CB']);
        ProductVariation::where(['color'=>'Vermelho'])->update(['color'=>'#FF0000']);
        ProductVariation::where(['color'=>'Cinza'])->update(['color'=>'#808080']);
        ProductVariation::where(['color'=>'Branco'])->update(['color'=>'#FFFFFF']);
        ProductVariation::where(['color'=>'Preto'])->update(['color'=>'#000000']);

    }
}
