<?php
// Vercel runtime configuration
if (getenv('VERCEL')) {
    // Use stderr for logging in Vercel
    putenv('LOG_STACK=stderr');
    
    // Configure config cache to use a temporary accessible path
    if (!getenv('APP_CONFIG_CACHE')) {
        putenv('APP_CONFIG_CACHE=/tmp/laravel-config-' . uniqid() . '.php');
    }
    
    // Ensure storage directory has proper permissions
    $storagePath = __DIR__ . '/../storage';
    if (is_dir($storagePath)) {
        @chmod($storagePath, 0777);
        foreach (['framework', 'logs', 'app'] as $dir) {
            $path = $storagePath . '/' . $dir;
            if (is_dir($path)) {
                @chmod($path, 0777);
            }
        }
    }
    
    // Clean cache files that might be corrupted from previous deployments
    $cacheFiles = [
        __DIR__ . '/../bootstrap/cache/config.php',
        __DIR__ . '/../bootstrap/cache/services.php',
        __DIR__ . '/../bootstrap/cache/packages.php',
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file) && is_file($file)) {
            @unlink($file);
        }
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    error_log('[API ERROR] ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
    
    // For debugging - remove in production
    if (getenv('APP_DEBUG') === 'true') {
        throw $e;
    }
    
    // Return a generic error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => getenv('APP_DEBUG') === 'true' ? $e->getMessage() : 'An error occurred'
    ]);
}