<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        $items = $purchase->items;
        $items->each(function ($item) use ($purchase) {
            $product = $purchase->product;
            $product->quantity -= $item->quantity;
        });
    }
}
