<?php
// Script de emergencia para arreglar contraseñas sin depender del Router/MVC
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración básica de BD (ajusta si tus credenciales son diferentes)
$host = '127.0.0.1';
$db   = 'consultores_chiriqui';
$user = 'win';
$pass = '12345'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión a BD: " . $e->getMessage());
}

$updates = [
    1 => 'empresa123',
    3 => 'admin123',
    7 => '12345',
    8 => '12345',
    9 => '12345',
    10 => '12345',
    11 => '12345'
];

echo "<h1>🛠️ Reparación de Contraseñas (Modo Directo)</h1>";

foreach ($updates as $id => $plainPass) {
    try {
        $hash = password_hash($plainPass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>✅ Usuario ID <strong>$id</strong> actualizado. Nueva pass: <code>$plainPass</code></p>";
        } else {
            echo "<p style='color:orange'>⚠️ Usuario ID <strong>$id</strong> no cambió (¿ya estaba arreglado o no existe?).</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Error en ID $id: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Proceso finalizado.</strong></p>";
echo "<p><a href='/consultores-chiriqui-platform/public/auth/login'>➡️ Ir al Login</a></p>";
