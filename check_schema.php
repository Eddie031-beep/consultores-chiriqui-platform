<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = db_connect('read');
    $stmt = $db->query("SHOW COLUMNS FROM facturas_detalle");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
