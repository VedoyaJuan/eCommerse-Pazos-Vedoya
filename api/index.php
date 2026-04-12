<?php

if (getenv('VERCEL')) {
    // Use bootstrap cache config file that always exists
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_STACK=stderr');
    
    // Make bootstrap/cache directory writable for PackageManifest
    $cacheDir = __DIR__ . '/../bootstrap/cache';
    if (is_dir($cacheDir)) {
        @chmod($cacheDir, 0777);
        @chmod($cacheDir . '/config.php', 0666);
        @chmod($cacheDir . '/packages.php', 0666);
    }
    
    // Ensure storage directory is writable
    $storagePath = __DIR__ . '/../storage';
    if (is_dir($storagePath)) {
        @chmod($storagePath, 0777);
        foreach (['app', 'framework', 'logs'] as $dir) {
            $path = $storagePath . '/' . $dir;
            if (is_dir($path)) {
                @chmod($path, 0777);
            }
        }
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    error_log('Fatal Error: ' . $e->getMessage());
    
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain');
    }
    
    echo "Internal Server Error";
    exit(1);
}