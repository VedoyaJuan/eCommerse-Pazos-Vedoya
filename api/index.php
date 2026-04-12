<?php
// Vercel runtime configuration
if (getenv('VERCEL')) {
    // Use stderr for logging
    putenv('LOG_STACK=stderr');
    
    // Don't use bootstrap cache files in Vercel (they can be stale)
    putenv('APP_CONFIG_CACHE=');
    
    // Ensure storage directory is writable
    $storagePath = __DIR__ . '/../storage';
    if (is_dir($storagePath)) {
        @chmod($storagePath, 0777);
    }
    
    // Clean stale cache files from previous deployments
    $cacheDir = __DIR__ . '/../bootstrap/cache';
    if (is_dir($cacheDir)) {
        foreach (['config.php', 'services.php', 'packages.php'] as $file) {
            $path = $cacheDir . '/' . $file;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    error_log('Error: ' . $e->getMessage());
    
    if (getenv('APP_DEBUG') === 'true') {
        throw $e;
    }
    
    http_response_code(500);
    echo "Internal Server Error";
}