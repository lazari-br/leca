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
        'purchase_price',
        'category_id',
        'subcategory',
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

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    // Método para obter a URL da imagem principal ou primeira disponível
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
