<?php
$APP_ENV = 'local';

// IPs reales de cada máquina
$WIN_HOST_APP = '127.0.0.1';  // Windows (XAMPP / MySQL maestro)
$UB_HOST      = '192.168.1.140';  // Ubuntu (MySQL réplica)

// Configuración de conexiones a BD
define('ENV_DB', [
    // Maestro: Windows
    'local' => [
        // Si el código corre en Windows, puedes usar '127.0.0.1' o la IP real.
        // Si quieres que también funcione cuando el código corra desde otra máquina,
        // usa la IP del Windows:
        'host'      => $WIN_HOST_APP,   // antes: '127.0.0.1'
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Réplica: Ubuntu
    'replica' => [
        'host'      => $UB_HOST,        // antes: '10.76.164.63'
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

    // Si entras desde el mismo Windows, localhost está bien.
    // Si quieres entrar desde otro dispositivo de la red, usarías:
    // 'BASE_URL' => 'http://192.168.1.105/ExamenFinalDS4/consultores-chiriqui-platform/public',
    'BASE_URL'   => 'http://localhost/ExamenFinalDS4/consultores-chiriqui-platform/public', 

    'ASSETS_URL' => '/ExamenFinalDS4/consultores-chiriqui-platform/public/assets',
]);
