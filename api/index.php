<?php

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Set environment variables for Vercel
if (getenv('VERCEL')) {
    putenv('LOG_CHANNEL=stderr');
    
    // Clear bootstrap cache to prevent Pail provider issue
    $cacheFiles = [
        __DIR__ . '/../bootstrap/cache/services.php',
        __DIR__ . '/../bootstrap/cache/packages.php',
        __DIR__ . '/../bootstrap/cache/config.php'
    ];
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }
    
    // Disable config cache on first load to allow clean bootstrap
    // putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    
    // Initialize database on first request
    $lockFile = '/tmp/db.initialized';
    if (!file_exists($lockFile)) {
        $initLock = @fopen('/tmp/db.init.lock', 'w');
        if ($initLock && flock($initLock, LOCK_EX | LOCK_NB)) {
            if (!file_exists($lockFile)) {
                // Create database directory and file
                $dbPath = '/tmp/database.sqlite';
                if (!file_exists($dbPath)) {
                    touch($dbPath);
                    chmod($dbPath, 0666);
                }
                
                // Run migrations
                try {
                    $app = require __DIR__ . '/../bootstrap/app.php';
                    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                    $kernel->call('migrate', ['--force' => true, '--database' => 'sqlite']);
                    unset($app, $kernel);
                } catch (\Exception $e) {
                    error_log('Migration error: ' . $e->getMessage());
                }
                
                // Mark database as initialized
                touch($lockFile);
            }
            flock($initLock, LOCK_UN);
            fclose($initLock);
        } else {
            if ($initLock) fclose($initLock);
            // Wait for lock to finish
            for ($i = 0; $i < 10; $i++) {
                if (file_exists($lockFile)) break;
                usleep(100000);
            }
        }
    }
}

require __DIR__ . '/../public/index.php';