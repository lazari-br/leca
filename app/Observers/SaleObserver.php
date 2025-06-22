<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SellerStock;

class SaleObserver
{
    public function created(Sale $sale)
    {
        $sale->items->each(function ($item) use ($sale) {
            $product = $sale->product;
            $product->quantity -= $item->quantity;
            if ($sale->seller_id) {
                $sellerStock = $this->getSellerProductStock($sale->seller_id, $product->id);
                $sellerStock->quantity += $item->quantity;
            }
        });
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
