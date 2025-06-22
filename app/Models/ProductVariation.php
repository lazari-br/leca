<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'stock',
        'active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
