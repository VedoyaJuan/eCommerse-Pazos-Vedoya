<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$basePath = dirname(__DIR__);

// Configure for Vercel serverless environment
if (getenv('VERCEL')) {
    // Set configuration cache path to a temporary writable location
    if (!getenv('APP_CONFIG_CACHE')) {
        putenv('APP_CONFIG_CACHE=/tmp/laravel-config.php');
    }
    
    // Clean bootstrap cache files that might be corrupted
    $cacheDir = $basePath . '/bootstrap/cache';
    if (is_dir($cacheDir)) {
        $filesToDelete = ['services.php', 'packages.php'];
        
        foreach ($filesToDelete as $file) {
            $path = $cacheDir . '/' . $file;
            if (file_exists($path) && is_file($path)) {
                @unlink($path);
            }
        }
    }
}

return Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();