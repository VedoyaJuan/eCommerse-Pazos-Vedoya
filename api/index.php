<?php

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Set environment variables for Vercel
if (getenv('VERCEL')) {
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');
}

// Boot the application from the public directory
require __DIR__ . '/../public/index.php';