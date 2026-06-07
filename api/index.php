<?php

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Set environment variables for Vercel
if (getenv('VERCEL')) {
    putenv('LOG_CHANNEL=stderr');
    
    // Clear bootstrap cache to prevent Pail provider issue
    $cacheFiles = [
        __DIR__ . '/../bootstrap/cache/services.php',
        __DIR__ . '/../bootstrap/cache/packages.php',
        __DIR__ . '/../bootstrap/cache/config.php'
    ];
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }
    
    // Disable config cache on first load to allow clean bootstrap
    // putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    
    // Initialize database on first request (only if using SQLite)
    $dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
    if ($dbConnection === 'sqlite') {
        $lockFile = '/tmp/db.initialized';
        if (!file_exists($lockFile)) {
            $initLock = @fopen('/tmp/db.init.lock', 'w');
            if ($initLock && flock($initLock, LOCK_EX | LOCK_NB)) {
                if (!file_exists($lockFile)) {
                    // Create database directory and file
                    $dbPath = '/tmp/database.sqlite';
                    if (!file_exists($dbPath)) {
                        touch($dbPath);
                        chmod($dbPath, 0666);
                    }
                    
                    // Run migrations
                    try {
                        $app = require __DIR__ . '/../bootstrap/app.php';
                        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                        $kernel->call('migrate', ['--force' => true, '--database' => 'sqlite']);
                        unset($app, $kernel);
                    } catch (\Throwable $e) {
                        error_log('Migration error: ' . $e->getMessage());
                    }
                    
                    // Mark database as initialized
                    touch($lockFile);
                }
                flock($initLock, LOCK_UN);
                fclose($initLock);
            } else {
                if ($initLock) fclose($initLock);
                // Wait for lock to finish
                for ($i = 0; $i < 10; $i++) {
                    if (file_exists($lockFile)) break;
                    usleep(100000);
                }
            }
        }
    }
}

// ------------------------------------------------------------------
// BOOTSTRAP LA APLICACIÓN Y CONFIGURACIÓN SERVERLESS
// ------------------------------------------------------------------

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Redirigir el storage al directorio temporal (Vercel/Lambdas)
$app->useStoragePath($_ENV['APP_STORAGE'] ?? '/tmp/storage');

// Crear la estructura de carpetas necesaria en el directorio temporal
$storagePath = $app->storagePath();
foreach (['/framework/views', '/framework/cache', '/framework/sessions', '/logs'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

// Inicializar/ejecutar migraciones para SQLite en /tmp si aplica
if (getenv('VERCEL')) {
    $dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
    if ($dbConnection === 'sqlite') {
        $lockFile = '/tmp/db.initialized';
        if (!file_exists($lockFile)) {
            $initLock = @fopen('/tmp/db.init.lock', 'w');
            if ($initLock && flock($initLock, LOCK_EX | LOCK_NB)) {
                if (!file_exists($lockFile)) {
                    $dbPath = '/tmp/database.sqlite';
                    if (!file_exists($dbPath)) {
                        touch($dbPath);
                        @chmod($dbPath, 0666);
                    }

                    try {
                        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                        $kernel->call('migrate', ['--force' => true, '--database' => 'sqlite']);
                        unset($kernel);
                    } catch (\Throwable $e) {
                        error_log('Migration error: ' . $e->getMessage());
                    }

                    touch($lockFile);
                }
                flock($initLock, LOCK_UN);
                fclose($initLock);
            } else {
                if ($initLock) fclose($initLock);
                for ($i = 0; $i < 10; $i++) {
                    if (file_exists($lockFile)) break;
                    usleep(100000);
                }
            }
        }
    }
}

// Manejar la petición HTTP
$app->handleRequest(\Illuminate\Http\Request::capture());