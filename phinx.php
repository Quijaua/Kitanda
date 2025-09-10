<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'autoload' => 'vendor/autoload.php',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host'    => $_ENV['DB_HOST'],
            'name'    => $_ENV['DB_NAME'],
            'user'    => $_ENV['DB_USERNAME'],
            'pass'    => $_ENV['DB_PASSWORD'],
            'port'    => $_ENV['DB_PORT'],
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'mysql_db',
            'name' => getenv('DB_NAME') ?: 'app_db',
            'user' => getenv('DB_USERNAME') ?: 'root',
            'pass' => getenv('DB_PASSWORD') ?: 'root',
            'port' => getenv('DB_PORT') ?: 3306,
            'charset' => 'utf8mb4',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host'    => $_ENV['DB_HOST'],
            'name'    => $_ENV['DB_NAME_TESTING'] ?? $_ENV['DB_NAME'],
            'user'    => $_ENV['DB_USERNAME'],
            'pass'    => $_ENV['DB_PASSWORD'],
            'port'    => $_ENV['DB_PORT'],
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];