<?php

function set_default_env(string $key, string $value): void
{
    if (getenv($key) !== false || isset($_ENV[$key]) || isset($_SERVER[$key])) {
        return;
    }

    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

set_default_env('APP_ENV', 'production');
set_default_env('APP_DEBUG', 'false');
set_default_env('LOG_CHANNEL', 'stderr');
set_default_env('CACHE_STORE', 'array');
set_default_env('SESSION_DRIVER', 'cookie');
set_default_env('QUEUE_CONNECTION', 'sync');
set_default_env('LARAVEL_STORAGE_PATH', '/tmp/storage');
set_default_env('VIEW_COMPILED_PATH', '/tmp/storage/framework/views');
set_default_env('APP_CONFIG_CACHE', '/tmp/storage/framework/cache/config.php');
set_default_env('APP_EVENTS_CACHE', '/tmp/storage/framework/cache/events.php');
set_default_env('APP_PACKAGES_CACHE', '/tmp/storage/framework/cache/packages.php');
set_default_env('APP_ROUTES_CACHE', '/tmp/storage/framework/cache/routes.php');
set_default_env('APP_SERVICES_CACHE', '/tmp/storage/framework/cache/services.php');

foreach ([
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__.'/../public/index.php';
