<?php
// Logging para debugging en Vercel
error_log('=== API INDEX STARTED ===');
error_log('APP_ENV: ' . (getenv('APP_ENV') ?: 'NOT SET'));
error_log('APP_KEY: ' . (getenv('APP_KEY') ? 'SET' : 'NOT SET'));

try {
    error_log('Requiring public/index.php');
    require __DIR__ . '/../public/index.php';
    error_log('=== API INDEX COMPLETED ===');
} catch (Exception $e) {
    error_log('ERROR in api/index.php: ' . $e->getMessage());
    error_log('Stack: ' . $e->getTraceAsString());
    throw $e;
}