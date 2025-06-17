<?php

use App\Http\Controllers\ChatAIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductAdminController;

// Rota Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de Produtos
// A rota abaixo precisa ser atualizada para incluir um prefixo como 'produto/' antes do slug
Route::get('/produto/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/categoria/{slug}', [ProductController::class, 'category'])->name('product.category');
Route::get('/categoria/{categorySlug}/{subcategory}', [ProductController::class, 'subcategory'])->name('product.subcategory');

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas administrativas
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Painel principal do Admin
    Route::get('/', function () {
        return view('admin.index');
    })->name('index');

    // Rotas para gerenciamento de produtos
    Route::get('/products', [ProductAdminController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductAdminController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('products.update');
    Route::post('/products/{id}', [ProductAdminController::class, 'update'])->name('products.update.post');
    Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('products.destroy');

    // Rotas para gerenciamento de imagens de produtos
    Route::post('/products/{id}/images/main/{imageId}', [ProductAdminController::class, 'setMainImage'])->name('products.images.main');
    Route::delete('/products/{id}/images/{imageId}', [ProductAdminController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('/products/{id}/images/reorder', [ProductAdminController::class, 'reorderImages'])->name('products.images.reorder');

    // Compras
    Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/compras/criar', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/compras', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/compras/{id}/editar', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/compras/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/compras/{id}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    // Vendas
    Route::get('/vendas', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/vendas/criar', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/vendas', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/vendas/{id}/editar', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/vendas/{id}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/vendas/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');

    Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboards.index');
    Route::get('/dashboards/cash-flow', [DashboardController::class, 'cashFlow'])->name('dashboards.cash-flow');
    Route::get('/dashboards/sales-report', [DashboardController::class, 'salesReport'])->name('dashboards.sales-report');
    Route::get('/dashboards/purchases-report', [DashboardController::class, 'purchasesReport'])->name('dashboards.purchases-report');

    Route::get('/purchases/export/total', [PurchaseController::class, 'exportTotal'])->name('purchases.export.total');
    Route::get('/purchases/export/monthly', [PurchaseController::class, 'exportMonthly'])->name('purchases.export.monthly');
    Route::get('/sales/export/total', [SalesController::class, 'exportTotal'])->name('sales.export.total');
    Route::get('/sales/export/monthly', [SalesController::class, 'exportMonthly'])->name('sales.export.monthly');

});

Route::post('/ia-chat', [ChatAIController::class, 'respond']);
Route::post('/ia-chat/reset', [ChatAIController::class, 'reset']);
Route::get('/ia-chat/history', [ChatAIController::class, 'history']);
