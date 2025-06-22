<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image_path',
        'is_main',
        'order'
    ];

    public function getImageUrlAttribute(): string
    {
        return 'https://storage.googleapis.com/leca_storage/' . $this->image_path;
    }

    public function getUrl(): string
    {
        return $this->image_url;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
