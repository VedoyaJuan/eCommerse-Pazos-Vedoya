<?php

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

if (getenv('VERCEL')) {
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');
    putenv('CACHE_DRIVER=array');
    putenv('SESSION_DRIVER=cookie');

    // Initialize SQLite DB and run migrations on first request (thread-safe with file lock)
    $dbPath = '/tmp/database.sqlite';
    $lockFile = '/tmp/database.lock';
    
    if (!file_exists($dbPath)) {
        // Use lock file to prevent race conditions
        $lock = @fopen($lockFile, 'w');
        if ($lock && flock($lock, LOCK_EX | LOCK_NB)) {
            // We have the lock, check again if db exists (another process might have created it)
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0666);
                
                try {
                    $app = require __DIR__ . '/../bootstrap/app.php';
                    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                    $kernel->call('migrate', ['--force' => true, '--database' => 'sqlite']);
                } catch (\Exception $e) {
                    error_log('Migration error: ' . $e->getMessage());
                }
            }
            flock($lock, LOCK_UN);
            fclose($lock);
            @unlink($lockFile);
        } elseif ($lock) {
            fclose($lock);
            // Another process is initializing, wait a bit
            usleep(500000);
        }
    }
}

require __DIR__ . '/../public/index.php';