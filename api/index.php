<?php

if (getenv('VERCEL')) {
    // Disable config cache BEFORE including public/index.php
    putenv('APP_CONFIG_CACHE=');
    putenv('LOG_STACK=stderr');
    
    // Ensure storage directory is writable
    $storagePath = __DIR__ . '/../storage';
    if (is_dir($storagePath)) {
        @chmod($storagePath, 0777);
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