<?php
// config/database.php

require_once __DIR__ . '/env.php';

function db_connect(string $target = 'local'): PDO
{
    if (!defined('ENV_DB') || !isset(ENV_DB[$target])) {
        throw new RuntimeException("Configuración de BD '$target' no encontrada.");
    }

    $cfg = ENV_DB[$target];

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['port'],
        $cfg['db'],
        $cfg['charset']
    );

    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}
