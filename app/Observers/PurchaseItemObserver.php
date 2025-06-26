<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    public function created(PurchaseItem $purchaseItem): void
    {
        $sku = $purchaseItem->variation;
        $sku->increment('stock', $purchaseItem->quantity);
    }
}
