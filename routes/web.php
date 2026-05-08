<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;

// Storefront routes
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::get('/product/{product}', [StoreController::class, 'show'])->name('store.show');

// Admin routes
Route::resource('products', ProductController::class);