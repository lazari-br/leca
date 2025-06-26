<?php

namespace App\Observers;

use App\Models\SellerStock;
use Illuminate\Support\Facades\Log;

class SellerStockObserver
{
    public function created(SellerStock $sellerStock): void
    {
        Log::info('SellerStockObserver Created');
        $sellerStock->productVariation->decrement('stock', $sellerStock->quantity);
    }

    public function updated(SellerStock $sellerStock): void
    {
        $sellerStock->productVariation->decrement(
            'stock', ($sellerStock->getChanges()['quantity'] - $sellerStock->getOriginal()['quantity'])
        );
    }

    public function deleting($sellerStock): void
    {
        $sellerStock->productVariation->increment('stock', $sellerStock->quantity);
    }
}
