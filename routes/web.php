<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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

// Protected routes
Route::middleware('auth')->group(function () {
    // Redirect root to products index
    Route::get('/', function () {
        return redirect()->route('products.index');
    })->name('home');

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