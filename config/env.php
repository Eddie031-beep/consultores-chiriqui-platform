<?php
// config/env.php

$APP_ENV = 'local';

// - App PHP corre en Windows → se conecta a MySQL maestro por 127.0.0.1
// - 192.168.1.106 = IP LAN de Windows (la usa Ubuntu para replicación, a nivel MySQL)
// - 192.168.1.140 = IP LAN de Ubuntu (la usa la app para leer de la réplica)

$WIN_HOST_APP = '127.0.0.1';      // Host que usa la APLICACIÓN PHP en Windows
$WIN_HOST_LAN = '192.168.1.106';  // Host que usa Ubuntu en CHANGE MASTER (documental)
$UB_HOST      = '192.168.1.140';  // Host de la VM Ubuntu (MySQL réplica)

define('ENV_DB', [
    // Maestro: MySQL en Windows (XAMPP)
    'local' => [
        'host'      => $WIN_HOST_APP,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',       // ← aquí usamos el usuario que ya tenías
        'pass'      => '12345',     // ← misma clave
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    // Réplica: MySQL en Ubuntu (solo lectura desde PHP)
    'replica' => [
        'host'      => $UB_HOST,
        'port'      => 3306,
        'db'        => 'consultores_chiriqui',
        'user'      => 'win',       // mismo usuario en Ubuntu
        'pass'      => '12345',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
]);

define('ENV_APP', [
    'APP_ENV'    => $APP_ENV,
    'BASE_URL'   => 'http://localhost/ExamenFinalDS4/consultores-chiriqui-platform/public',
    'ASSETS_URL' => '/ExamenFinalDS4/consultores-chiriqui-platform/public/assets',
]);
