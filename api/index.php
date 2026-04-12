<?php

if (getenv('VERCEL')) {
    putenv('APP_CONFIG_CACHE=' . __DIR__ . '/../bootstrap/cache/config.php');
    putenv('LOG_CHANNEL=stderr');
}

require __DIR__ . '/../public/index.php';