<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// En Vercel (read-only filesystem), no usar bootstrap cache
$basePath = dirname(__DIR__);
if (getenv('VERCEL')) {
    // Eliminar archivos de cache que causan problemas
    @unlink($basePath . '/bootstrap/cache/services.php');
    @unlink($basePath . '/bootstrap/cache/packages.php');
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