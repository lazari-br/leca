<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SellerStock;

class SaleItemObserver
{
    public function created(SaleItem $saleItem)
    {
        $product = $saleItem->variation;
        $product->decrement('stock', $saleItem->quantity);

        if ($saleItem->sale->seller_id) {
            $sellerStock = $this->getSellerProductStock($saleItem->sale->seller_id, $product->id);
            $sellerStock->decrement('quantity', $saleItem->quantity);
        }
    }

    private function getSellerProductStock(int $sellerId, int $productId): SellerStock
    {
        return SellerStock::where([
            'seller_id' => $sellerId,
            'product_id' => $productId
        ])
            ->first();
    }
}
