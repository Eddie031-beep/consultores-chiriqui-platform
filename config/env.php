<?php
$APP_ENV = 'local';

/*
|--------------------------------------------------------------------------
| Hostnames en lugar de IPs
|--------------------------------------------------------------------------
| Gracias al archivo hosts en Windows y Ubuntu, estos nombres apuntarán
| siempre a la IP correcta, aunque cambie el día del examen.
|--------------------------------------------------------------------------
*/
$WIN_HOST_APP = 'win-main-db';     // Windows (MySQL maestro)
$UB_HOST      = 'ub-replica-db';   // Ubuntu (MySQL réplica)

/*
|--------------------------------------------------------------------------
| Configuración de conexiones a la Base de Datos
|--------------------------------------------------------------------------
| 'local'   → BD principal (Windows, maestro)
| 'replica' → BD réplica (Ubuntu)
|--------------------------------------------------------------------------
*/
define('ENV_DB', [
    'local' => [
        'host'      => $WIN_HOST_APP,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',       // tu usuario real en Windows
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'replica' => [
        'host'      => $UB_HOST,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',       // usuario real en Ubuntu
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
]);

/*
|--------------------------------------------------------------------------
| Configuración de la APP
|--------------------------------------------------------------------------
*/
define('ENV_APP', [
    'APP_ENV'    => $APP_ENV,
    'BASE_URL'   => 'http://localhost/ExamenFinalDS4/consultores-chiriqui-platform/public',
    'ASSETS_URL' => '/ExamenFinalDS4/consultores-chiriqui-platform/public/assets',
]);
