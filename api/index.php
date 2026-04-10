<?php
// En Vercel, limpiar cache de optimización en cada request (read-only filesystem hace que cache sea inconsistente)
if (getenv('VERCEL')) {
    $artisan = __DIR__ . '/../artisan';
    if (file_exists($artisan)) {
        // Limpiar cache de optimización para que Laravel se reinicialice
        exec('php ' . escapeshellarg($artisan) . ' optimize:clear 2>&1 > /dev/null || true');
    }
    // Usar stderr para logging
    putenv('LOG_STACK=stderr');
}

error_log('=== API INDEX STARTED ===');
error_log('APP_ENV: ' . (getenv('APP_ENV') ?: 'NOT SET'));

try {
    require __DIR__ . '/../public/index.php';
} catch (Exception $e) {
    error_log('ERROR: ' . $e->getMessage());
    throw $e;
}