<?php

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

if (getenv('VERCEL')) {
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');
    putenv('CACHE_DRIVER=array');
    putenv('SESSION_DRIVER=cookie');
}

require __DIR__ . '/../public/index.php';