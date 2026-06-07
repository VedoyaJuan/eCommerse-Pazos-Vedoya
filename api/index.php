<?php

// 1. Registrar el autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. Configurar variables de entorno iniciales
if (getenv('VERCEL')) {
    putenv('LOG_CHANNEL=stderr');
    
    // Limpiar caché de bootstrap si existe
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
}

// 3. Inicializar la aplicación
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Redirigir el storage al directorio temporal de Vercel
$app->useStoragePath($_ENV['APP_STORAGE'] ?? '/tmp/storage');

// Crear la estructura de carpetas necesaria en el directorio temporal
$storagePath = $app->storagePath();
foreach (['/framework/views', '/framework/cache', '/framework/sessions', '/logs'] as $path) {
    if (!is_dir($storagePath . $path)) {
        @mkdir($storagePath . $path, 0777, true);
    }
}

// 5. Procesar la petición capturando el error real
try {
    $request = Illuminate\Http\Request::capture();
    $response = $app->handleRequest($request);
    $response->send();
    $app->terminate($request, $response);
} catch (\Throwable $e) {
    // Esto intercepta el error REAL
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "🚨 ERROR FATAL ORIGINAL:\n\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " (Línea: " . $e->getLine() . ")\n\n";
    echo "Traza:\n" . $e->getTraceAsString();
    exit;
}