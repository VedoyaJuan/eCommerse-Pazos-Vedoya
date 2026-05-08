<?php
/**
 * Database initialization for Vercel
 * This runs once to set up the database and migrations
 */

if (!getenv('VERCEL')) {
    exit('This script is only for Vercel environment');
}

require __DIR__ . '/vendor/autoload.php';

$lockFile = '/tmp/db.initialized';

// Check if already initialized
if (file_exists($lockFile)) {
    exit(0);
}

// Create lock to prevent race conditions
$lock = @fopen('/tmp/db.init.lock', 'w');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    // Wait for other process to finish
    usleep(1000000);
    exit(0);
}

try {
    // Set environment
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');
    
    // Create database
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        chmod($dbPath, 0666);
    }
    
    // Load and run migrations
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    
    // Run migrations
    $exitCode = $kernel->call('migrate', [
        '--force' => true,
        '--database' => 'sqlite'
    ]);
    
    // Mark as initialized
    touch($lockFile);
    
    exit($exitCode ?? 0);
} catch (\Exception $e) {
    error_log('Database initialization failed: ' . $e->getMessage());
    exit(1);
} finally {
    if ($lock) {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}
