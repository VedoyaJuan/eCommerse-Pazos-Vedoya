<?php
// Para Vercel: limpiar cache corrupto y usar configuración dinámica
if (getenv('VERCEL')) {
    putenv('LOG_STACK=stderr');
    
    // Limpiar archivos de cache que pueden estar corruptos entre requests
    $cacheFiles = [
        __DIR__ . '/../bootstrap/cache/config.php',
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }
    
    // Forzar regeneración sin usar cached config
    putenv('APP_CONFIG_CACHE=');
}

error_log('[API] Starting request for ' . ($_SERVER['REQUEST_URI'] ?? '/'));

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    error_log('[API ERROR] ' . $e->getMessage() .  ' at ' . $e->getFile() . ':' . $e->getLine());
    throw $e;
}