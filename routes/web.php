<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

// Auth routes (public)
Route::get('/debug-route', function (\Illuminate\Http\Request $request) {
    return [
        'request_uri' => $_SERVER['REQUEST_URI'] ?? null,
        'path' => $request->path(),
        'url' => $request->url(),
        'fullUrl' => $request->fullUrl(),
    ];
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Storefront routes (Public)
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/product/{product}', [StoreController::class, 'show'])->name('store.show');

// Cart routes (Public)
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/checkout', [\App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');
Route::get('/checkout/success', [\App\Http\Controllers\CartController::class, 'success'])->name('cart.success');

// Protected routes
Route::middleware('auth')->group(function () {
    // Admin routes
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

    // Admin-only: user management
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::patch('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});