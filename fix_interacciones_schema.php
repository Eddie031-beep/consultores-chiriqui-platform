<?php
// fix_interacciones_schema.php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

$db = db_connect('local');

try {
    echo "<h1>Fixing Schema: interacciones_vacante</h1>";
    
    // 1. Check if table exists
    $db->query("SELECT 1 FROM interacciones_vacante LIMIT 1");
    
    // 2. Modify tipo_interaccion to be VARCHAR(50) to allow 'ver_detalle' and others
    echo "Modifying tipo_interaccion to VARCHAR(50)...\n";
    $db->query("ALTER TABLE interacciones_vacante MODIFY COLUMN tipo_interaccion VARCHAR(50) NOT NULL");
    
    // 3. Inspect if 'estado' column exists and what it is
    echo "Checking for 'estado' column...\n";
    $stmt = $db->query("DESCRIBE interacciones_vacante");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasEstado = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'estado') {
            $hasEstado = true;
            echo "Found column 'estado' type: " . $col['Type'] . "\n";
        }
    }

    if ($hasEstado) {
        // If it exists and we're not using it, maybe drop it or make it nullable?
        // Let's make it nullable and VARCHAR just in case
        echo "Modifying 'estado' to VARCHAR(50) NULL...\n";
        $db->query("ALTER TABLE interacciones_vacante MODIFY COLUMN estado VARCHAR(50) NULL");
    }

    echo "✅ Schema Fixed.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
