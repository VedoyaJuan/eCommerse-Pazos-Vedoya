<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MercadoPagoController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Public order creation (guest or authenticated)
Route::post('/orders', [OrderController::class, 'store']);

// Public guest order access
Route::get('/orders/guest/{token}', [OrderController::class, 'showGuest']);
Route::patch('/orders/guest/{token}/cancel', [OrderController::class, 'cancelGuest']);

// Webhook de Mercado Pago (público — MP llama desde sus servidores)
Route::post('/webhook/mercadopago', [MercadoPagoController::class, 'webhook']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // User order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Admin/Vendedor product routes (CRUD)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Admin/Vendedor global orders management
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::get('/admin/orders/{order}', [OrderController::class, 'adminShow']);
    Route::patch('/admin/orders/{order}', [OrderController::class, 'adminUpdate']);

    // Admin-only user/vendor management
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::patch('/admin/users/{user}/approve', [UserController::class, 'approve']);
        Route::patch('/admin/users/{user}/reject', [UserController::class, 'reject']);
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
    });
});
