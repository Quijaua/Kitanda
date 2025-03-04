<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'kitanda',
            'user' => 'root',
            'pass' => '',
            'port' => '3312',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'kitanda',
            'user' => 'root',
            'pass' => '',
            'port' => '3312',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'kitanda',
            'user' => 'root',
            'pass' => '',
            'port' => '3312',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
