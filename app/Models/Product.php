<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'purchase_price',
        'category_id',
        'subcategory',
        'active'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function sellerStocks(): HasMany
    {
        return $this->hasMany(SellerStock::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Escopo para produtos ativos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getMainImageUrlAttribute()
    {
        // Verifica se existe uma imagem principal
        $mainImage = $this->mainImage;
        if ($mainImage) {
            return asset($mainImage->image_path);
        }

        // Verifica se existe alguma imagem
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return asset($firstImage->image_path);
        }

        // Caso não haja imagens, verifica o campo 'image' legado
        if ($this->image) {
            return asset($this->image);
        }

        // Sem imagem
        return null;
    }

    public function getAvailableSizesAttribute()
    {
        return $this->variations()->pluck('size')->unique()->toArray();
    }

    public function getAvailableColorsAttribute()
    {
        return $this->variations()->pluck('color')->unique()->toArray();
    }

    public function getMainImagePathAttribute()
    {
        // Primeiro tenta buscar a imagem principal da relação
        if ($this->images->isNotEmpty()) {
            $mainImage = $this->images->where('is_main', true)->first();

            if ($mainImage) {
                return $mainImage->image_path;
            }

            // Se não tem imagem principal, usa a primeira
            return $this->images->first()->image_path;
        }

        // Se não tem imagens na relação, tenta usar o campo de imagem legado
        if ($this->image) {
            return $this->image;
        }

        // Se não tem nenhuma imagem, retorna null
        return null;
    }
}
