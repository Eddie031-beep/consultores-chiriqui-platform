<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');

echo "<h1>🔐 Credenciales de Acceso (Entorno de Pruebas)</h1>";
echo "<p> Nota: Las contraseñas están encriptadas. Se muestran las predeterminadas si son conocidas.</p>";

// 1. EMPRESAS / ADMINS
echo "<h2>🏢 Empresas y Administrativos (Tabla: usuarios)</h2>";
try {
    $stmt = $db->query("SELECT u.id, u.nombre, u.apellido, u.email, r.nombre as rol, e.nombre as empresa 
                        FROM usuarios u 
                        LEFT JOIN roles r ON u.rol_id = r.id
                        LEFT JOIN empresas e ON u.empresa_id = e.id");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background:#f0f0f0'><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Empresa</th><th>Contraseña (Probable)</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Logica simple para deducir contraseñas de test comunes
        $pass = '<i>Desconocida (Hash)</i>';
        if (strpos($row['email'], 'admin') !== false) $pass = '123456 (Común)';
        if (strpos($row['email'], 'test') !== false) $pass = '123456 (Común)';
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nombre']} {$row['apellido']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['rol']}</td>";
        echo "<td>{$row['empresa']}</td>";
        echo "<td>{$pass}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) { echo $e->getMessage(); }

// 2. CANDIDATOS
echo "<h2>👨‍💼 Candidatos (Tabla: solicitantes)</h2>";
try {
    $stmt = $db->query("SELECT id, nombre, apellido, email, perfil_completado FROM solicitantes");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background:#f0f0f0'><th>ID</th><th>Nombre</th><th>Email</th><th>Perfil Completo?</th><th>Contraseña (Probable)</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pass = '<i>Desconocida (Hash)</i>';
        if ($row['email'] == 'candidato@test.com') $pass = '123456';
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nombre']} {$row['apellido']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>" . ($row['perfil_completado'] ? '✅ Sí' : '❌ No') . "</td>";
        echo "<td>{$pass}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) { echo $e->getMessage(); }
