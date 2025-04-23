<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'price',
        'category_id',
        'subcategory',
        'image',
        'active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function getAvailableSizesAttribute()
    {
        return $this->variations()->pluck('size')->unique()->toArray();
    }

    public function getAvailableColorsAttribute()
    {
        return $this->variations()->pluck('color')->unique()->toArray();
    }
}
