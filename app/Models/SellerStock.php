<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerStock extends Model
{

    protected $table = 'seller_stocks';

    protected $fillable = [
        'seller_id',
        'product_id',
        'quantity',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
