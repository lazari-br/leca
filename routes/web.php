<?php

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
    // Rotas para gerenciamento de produtos
    Route::get('/products', [ProductAdminController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductAdminController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('products.destroy');
    
    // Rotas para gerenciamento de imagens de produtos
    Route::post('/products/{id}/images/main/{imageId}', [ProductAdminController::class, 'setMainImage'])->name('products.images.main');
    Route::delete('/products/{id}/images/{imageId}', [ProductAdminController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('/products/{id}/images/reorder', [ProductAdminController::class, 'reorderImages'])->name('products.images.reorder');
});