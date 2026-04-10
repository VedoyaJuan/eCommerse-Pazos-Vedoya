<?php
/**
 * Inicialización de optimizaciones para Vercel
 * Se ejecuta una sola vez cuando el servidor se inicia por primera vez
 */

if (getenv('VERCEL') && !file_exists(storage_path('.vercel_initialized'))) {
    try {
        // Usar Symfony Process para ejecutar artisan commands
        $process = new \Symfony\Component\Process\Process(['php', 'artisan', 'config:cache'], dirname(__DIR__));
        $process->run();
        
        // Marcar como inicializado
        @mkdir(storage_path(), 0755, true);
        file_put_contents(storage_path('.vercel_initialized'), date('Y-m-d H:i:s'));
        
        error_log('Vercel initialized successfully');
    } catch (Exception $e) {
        // Silenciosamente fallar si hay errores
        error_log('Vercel init notice: ' . $e->getMessage());
    }
}
