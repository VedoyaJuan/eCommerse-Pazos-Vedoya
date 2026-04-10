<?php
/**
 * Inicialización de optimizaciones para Vercel
 * Se ejecuta una sola vez cuando el servidor se inicia por primera vez
 */

$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage';
$initMarker = $storagePath . '/.vercel_initialized';

if (getenv('VERCEL') && !file_exists($initMarker)) {
    try {
        // Crear directorio de storage si no existe
        @mkdir($storagePath, 0755, true);
        
        // Usar Symfony Process para ejecutar artisan command
        $process = new \Symfony\Component\Process\Process(['php', 'artisan', 'config:cache'], $basePath);
        $process->run();
        
        // Marcar como inicializado
        file_put_contents($initMarker, date('Y-m-d H:i:s'));
        error_log('Vercel initialized successfully');
    } catch (Exception $e) {
        // Log silencioso - no interrumpir la aplicación
        error_log('Vercel init notice: ' . $e->getMessage());
    }
}
