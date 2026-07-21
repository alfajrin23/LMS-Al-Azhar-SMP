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

function force_env(string $key, string $value): void
{
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

force_env('APP_ENV', 'production');
set_default_env('APP_KEY', 'base64:A0HS5iKNXPcJ73zKN2m00AudKedDw4Zk3mUB0pPHXuQ=');
force_env('APP_DEBUG', 'false');
set_default_env('APP_URL', 'https://'.(getenv('VERCEL_URL') ?: 'lms-al-azhar-smp.vercel.app'));
force_env('LOG_CHANNEL', 'stderr');
force_env('CACHE_STORE', 'array');
force_env('SESSION_DRIVER', 'cookie');
force_env('QUEUE_CONNECTION', 'sync');
force_env('DB_CONNECTION', 'sqlite');
force_env('DB_DATABASE', '/tmp/database.sqlite');
force_env('LARAVEL_STORAGE_PATH', '/tmp/storage');
force_env('VIEW_COMPILED_PATH', '/tmp/storage/framework/views');
force_env('APP_CONFIG_CACHE', '/tmp/storage/framework/cache/config.php');
force_env('APP_EVENTS_CACHE', '/tmp/storage/framework/cache/events.php');
force_env('APP_PACKAGES_CACHE', '/tmp/storage/framework/cache/packages.php');
force_env('APP_ROUTES_CACHE', '/tmp/storage/framework/cache/routes.php');
force_env('APP_SERVICES_CACHE', '/tmp/storage/framework/cache/services.php');

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

$sourceDatabase = __DIR__.'/../database/database.sqlite';
$runtimeDatabase = '/tmp/database.sqlite';
$runtimeDatabaseVersion = '/tmp/database.sqlite.version';

if (file_exists($sourceDatabase)) {
    $sourceDatabaseVersion = filesize($sourceDatabase).':'.filemtime($sourceDatabase);
    $currentDatabaseVersion = file_exists($runtimeDatabaseVersion)
        ? trim((string) file_get_contents($runtimeDatabaseVersion))
        : null;
}

if (file_exists($sourceDatabase) && (! file_exists($runtimeDatabase) || $currentDatabaseVersion !== $sourceDatabaseVersion)) {
    copy($sourceDatabase, $runtimeDatabase);
    file_put_contents($runtimeDatabaseVersion, $sourceDatabaseVersion);
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__.'/../public/index.php';
