<?php

if (getenv('VERCEL')) {
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');

    // Initialize SQLite DB in /tmp if it doesn't exist, then run migrations
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath) || filesize($dbPath) === 0) {
        touch($dbPath);
        $app = require __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->call('migrate', ['--force' => true]);
        unset($app, $kernel);
    }
}

require __DIR__ . '/../public/index.php';