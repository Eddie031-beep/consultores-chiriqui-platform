<?php
// config/env.php

$APP_ENV = 'local';

define('ENV_DB', [
    'local' => [
        'host'      => '127.0.0.1',
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'root',
        'pass'      => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'replica' => [
        'host'      => '127.0.0.1',
        'port'      => 3307,
        'db'        => 'consultores_chiriqui',
        'user'      => 'root',
        'pass'      => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
]);

define('ENV_APP', [
    'APP_ENV'    => $APP_ENV,
    'BASE_URL'   => 'http://localhost/ExamenFinalDS4/consultores-chiriqui-platform/public',
    'ASSETS_URL' => '/ExamenFinalDS4/consultores-chiriqui-platform/public/assets',
]);
