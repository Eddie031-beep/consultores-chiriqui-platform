<?php
// Script to create default Consultant Admin
// Run this via browser: http://localhost/.../setup_admin.php
// Or CLI: php public/setup_admin.php

// DB Config (Hardcoded from env.php to be standalone/safe)
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
    
    // 1. Get Role ID
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'admin_consultora'");
    $stmt->execute();
    $role = $stmt->fetch();
    
    if (!$role) {
        // Create role if missing
        echo "Rol 'admin_consultora' no encontrado. Creándolo...\n";
        $pdo->exec("INSERT INTO roles (nombre, descripcion) VALUES ('admin_consultora', 'Usuario interno de la empresa consultora')");
        $role_id = $pdo->lastInsertId();
    } else {
        $role_id = $role['id'];
    }

    // 2. Check if user exists
    $email = 'admin@consultora.com';
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo "El usuario '$email' ya existe. Actualizando contraseña...\n";
        $password = password_hash('Admin123', PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE email = ?");
        $update->execute([$password, $email]);
        echo "Contraseña actualizada a: Admin123\n";
    } else {
        echo "Creando usuario '$email'...\n";
        $password = password_hash('Admin123', PASSWORD_BCRYPT);
        // empresa_id is NULL for consultants
        $insert = $pdo->prepare("INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id, estado) VALUES (NULL, 'Admin', 'Consultora', ?, ?, ?, 'activo')");
        $insert->execute([$email, $password, $role_id]);
        echo "Usuario creado exitosamente.\n";
    }

    echo "\n=== CREDENCIALES ===\n";
    echo "Email: $email\n";
    echo "Password: Admin123\n";
    echo "====================\n";

} catch (\PDOException $e) {
    echo "Error de Base de Datos: " . $e->getMessage();
}
?>
