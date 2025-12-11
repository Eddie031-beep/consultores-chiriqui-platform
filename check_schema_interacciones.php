<?php
// check_schema_interacciones.php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

$db = db_connect('local');

try {
    $response = [];
    
    $stmt = $db->query("DESCRIBE interacciones_vacante");
    $response['interacciones_vacante'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->query("DESCRIBE vacantes");
    $response['vacantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
