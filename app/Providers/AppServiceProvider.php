<?php

namespace App\Providers;

use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\SellerStock;
use App\Observers\PurchaseItemObserver;
use App\Observers\SaleItemObserver;
use App\Observers\SellerStockObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        SaleItem::observe(SaleItemObserver::class);
        PurchaseItem::observe(PurchaseItemObserver::class);
        SellerStock::observe(SellerStockObserver::class);
    }
}
