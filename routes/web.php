<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Rota principal - Catálogo
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rotas de produtos
Route::get('/produto/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/categoria/{slug}', [ProductController::class, 'category'])->name('product.category');
Route::get('/categoria/{categorySlug}/{subcategory}', [ProductController::class, 'subcategory'])->name('product.subcategory');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Rotas para o painel de administração (futuro)
});