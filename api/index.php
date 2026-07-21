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
set_default_env('APP_KEY', 'base64:A0HS5iKNXPcJ73zKN2m00AudKedDw4Zk3mUB0pPHXuQ=');
set_default_env('APP_DEBUG', 'false');
set_default_env('APP_URL', 'https://'.(getenv('VERCEL_URL') ?: 'lms-al-azhar-sd.vercel.app'));
set_default_env('LOG_CHANNEL', 'stderr');
set_default_env('CACHE_STORE', 'array');
set_default_env('SESSION_DRIVER', 'cookie');
set_default_env('QUEUE_CONNECTION', 'sync');
set_default_env('DB_CONNECTION', 'sqlite');
set_default_env('DB_DATABASE', '/tmp/database.sqlite');
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

$sourceDatabase = __DIR__.'/../database/database.sqlite';
$runtimeDatabase = '/tmp/database.sqlite';

if (! file_exists($runtimeDatabase) && file_exists($sourceDatabase)) {
    copy($sourceDatabase, $runtimeDatabase);
}

if (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) === '/_runtime-check') {
    header('Content-Type: application/json');

    echo json_encode([
        'php' => PHP_VERSION,
        'app_key_set' => getenv('APP_KEY') !== false,
        'db_connection' => getenv('DB_CONNECTION'),
        'db_database' => getenv('DB_DATABASE'),
        'source_database_exists' => file_exists($sourceDatabase),
        'runtime_database_exists' => file_exists($runtimeDatabase),
        'runtime_database_size' => file_exists($runtimeDatabase) ? filesize($runtimeDatabase) : null,
        'pdo_sqlite_loaded' => extension_loaded('pdo_sqlite'),
        'sqlite3_loaded' => extension_loaded('sqlite3'),
    ], JSON_PRETTY_PRINT);

    return;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__.'/../public/index.php';
