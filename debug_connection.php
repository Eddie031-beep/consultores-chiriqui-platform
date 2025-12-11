<?php
// debug_connection.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnosticando Conexión de Base de Datos</h1>";
echo "<pre>";

// 1. Verificar carga de configuración
if (!file_exists(__DIR__ . '/config/env.php')) {
    die("❌ Error Crítico: No se encuentra config/env.php");
}
require_once __DIR__ . '/config/env.php';

echo "✅ Archivo config/env.php cargado.\n";
echo "---------------------------------------\n";
echo "Configuración Detectada (ENV_DB['local']):\n";
if (defined('ENV_DB')) {
    $cfg = ENV_DB['local'];
    echo "Host: " . $cfg['host'] . "\n";
    echo "User: " . $cfg['user'] . "\n";
    echo "DB:   " . $cfg['db'] . "\n";
    echo "Port: " . $cfg['port'] . "\n";
} else {
    echo "❌ ENV_DB no está definida.\n";
}
echo "---------------------------------------\n";

// 2. Intentar Conexión
echo "Intentando conectar via PDO...\n";

try {
    $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['db']};charset={$cfg['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3
    ];
    
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
    echo "✅ ¡CONEXIÓN EXITOSA!\n";
    
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "Versión MySQL: " . $version . "\n";

} catch (PDOException $e) {
    echo "❌ ERROR DE CONEXIÓN:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
}

echo "</pre>";
