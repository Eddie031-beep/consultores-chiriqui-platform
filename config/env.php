<?php
$APP_ENV = 'local';

$WIN_HOST_APP = '127.0.0.1';      // PHP en Windows
$UB_HOST      = '192.168.1.140';  // IP de tu MySQL en Ubuntu

define('ENV_DB', [
    // Maestro: Windows
    'local' => [
        'host'      => $WIN_HOST_APP,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Réplica: Ubuntu
    'replica' => [
        'host'      => $UB_HOST,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
]);

define('ENV_APP', [
    'APP_ENV'    => $APP_ENV,
    // ASEGÚRATE DE QUE ESTA URL ES LA QUE INTENTAS VISITAR
    'BASE_URL'   => 'http://localhost/ExamenFinalDS4/consultores-chiriqui-platform/public', 
    'ASSETS_URL' => '/ExamenFinalDS4/consultores-chiriqui-platform/public/assets',
]);

