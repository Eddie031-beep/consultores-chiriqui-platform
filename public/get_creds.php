<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');
header('Content-Type: application/json');

$users = [];
try {
    // Get Empresas/Admins
    $stmt = $db->query("SELECT u.id, u.email, r.nombre as rol, e.nombre as empresa 
                        FROM usuarios u 
                        LEFT JOIN roles r ON u.rol_id = r.id
                        LEFT JOIN empresas e ON u.empresa_id = e.id");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = ['type' => 'User', 'email' => $row['email'], 'role' => $row['rol'], 'name' => $row['empresa'] ?: 'Admin'];
    }

    // Get Candidates
    $stmt = $db->query("SELECT nombre, apellido, email FROM solicitantes LIMIT 5");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = ['type' => 'Candidate', 'email' => $row['email'], 'role' => 'Candidato', 'name' => $row['nombre'] . ' ' . $row['apellido']];
    }
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
echo json_encode($users);
