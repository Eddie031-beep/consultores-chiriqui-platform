<?php
// Force error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/Config/Database.php';

try {
    $db = db_connect('local');
    echo "Connected to DB.\n";

    // 1. List current columns
    echo "Current Columns in 'facturas':\n";
    $stmt = $db->query("DESCRIBE facturas");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($columns);

    // 2. Add columns if missing
    $missing = [];
    if (!in_array('cufe', $columns)) $missing[] = "ADD COLUMN cufe VARCHAR(100) NULL AFTER token_publico";
    if (!in_array('protocolo_autorizacion', $columns)) $missing[] = "ADD COLUMN protocolo_autorizacion VARCHAR(50) NULL AFTER cufe";
    if (!in_array('clave_acceso', $columns)) $missing[] = "ADD COLUMN clave_acceso VARCHAR(100) NULL AFTER protocolo_autorizacion";
    if (!in_array('fecha_autorizacion', $columns)) $missing[] = "ADD COLUMN fecha_autorizacion DATETIME NULL AFTER clave_acceso";

    if (!empty($missing)) {
        echo "Adding missing columns...\n";
        foreach ($missing as $sql) {
            echo "Executing: ALTER TABLE facturas $sql\n";
            $db->query("ALTER TABLE facturas $sql");
        }
        echo "Columns added.\n";
    } else {
        echo "All DGI columns already exist.\n";
    }

    // 3. Verify again
    echo "Final Columns Check:\n";
    $stmt = $db->query("DESCRIBE facturas");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
