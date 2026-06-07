<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Forzar a Laravel a usar el directorio temporal de Vercel para absolutamente todo
$app->useStoragePath('/tmp/storage');
$app->useBootstrapPath('/tmp/bootstrap');

// Crear todas las carpetas de emergencia en el temporal
$tempDirectories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache'
];

foreach ($tempDirectories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Procesar la solicitud normalmente
$request = Illuminate\Http\Request::capture();
$response = $app->handleRequest($request);
$response->send();
$app->terminate($request, $response);