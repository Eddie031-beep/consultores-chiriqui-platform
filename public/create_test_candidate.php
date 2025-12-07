<?php
// Script to create default Candidate
// Run via CLI: php public/create_test_candidate.php

// require __DIR__ . '/../app/Config/Env.php'; // Removed causing error

// DB Config (Hardcoded for standalone script)
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
    echo "Conectando a la base de datos...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Check if user exists
    $email = 'candidato@test.com';
    $stmt = $pdo->prepare("SELECT id FROM solicitantes WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo "El usuario '$email' ya existe. Actualizando contraseña...\n";
        $password = password_hash('123456', PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE solicitantes SET password_hash = ? WHERE email = ?");
        $update->execute([$password, $email]);
        echo "Contraseña actualizada a: 123456\n";
    } else {
        echo "Creando usuario '$email'...\n";
        $password = password_hash('123456', PASSWORD_BCRYPT);
        
        $insert = $pdo->prepare("INSERT INTO solicitantes (nombre, apellido, email, password_hash, telefono, cedula, estado) VALUES ('Candidato', 'Prueba', ?, ?, '6000-0000', '4-000-000', 'activo')");
        $insert->execute([$email, $password]);
        echo "Usuario creado exitosamente.\n";
    }

    echo "\n=== CREDENCIALES CANDIDATO ===\n";
    echo "Email: $email\n";
    echo "Password: 123456\n";
    echo "==============================\n";

} catch (\PDOException $e) {
    echo "Error de Base de Datos: " . $e->getMessage();
}
?>
