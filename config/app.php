<?php

use App\Core\Env;

return [
    'app' => [
        'name' => Env::get('APP_NAME', 'AbeloHost Blog'),
        'env' => Env::get('APP_ENV', 'production'),
        'debug' => filter_var(Env::get('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
        'url' => Env::get('APP_URL', 'http://localhost'),
        'domain' => Env::get('APP_DOMAIN', 'localhost'),
    ],
    'db' => [
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => (int) Env::get('DB_PORT', '3306'),
        'database' => Env::get('DB_DATABASE', ''),
        'username' => Env::get('DB_USERNAME', ''),
        'password' => Env::get('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'pagination' => [
        'per_page' => (int) Env::get('PAGINATION_PER_PAGE', '6'),
    ],
    'paths' => [
        'root' => dirname(__DIR__),
        'templates' => dirname(__DIR__) . '/templates',
        'cache' => dirname(__DIR__) . '/storage/smarty/cache',
        'compile' => dirname(__DIR__) . '/storage/smarty/compile',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'assets' => '/assets',
    ],
];
