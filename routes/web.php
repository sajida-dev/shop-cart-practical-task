<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('products', [ProductController::class, 'index'])->name('products');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/cart/update/{itemId}', [CartController::class, 'updateQty'])->name('cart.updateQty');

    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
});


require __DIR__ . '/settings.php';
