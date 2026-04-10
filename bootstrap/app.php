<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$basePath = dirname(__DIR__);

// En Vercel (read-only filesystem con caching problemático), limpiar cache de bootstrap
if (getenv('VERCEL')) {
    $cacheDir = $basePath . '/bootstrap/cache';
    $filesToDelete = ['services.php', 'packages.php', 'config.php'];
    
    foreach ($filesToDelete as $file) {
        $path = $cacheDir . '/' . $file;
        if (file_exists($path)) {
            @unlink($path);
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