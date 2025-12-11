<?php
require_once __DIR__ . '/config/database.php';

// Use the existing connection function
try {
    $pdo = db_connect('write');
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

function addColumnIfNotExists($pdo, $table, $column, $definition) {
    echo "Checking column '$column' in table '$table'...\n";
    $stmt = $pdo->query("DESCRIBE $table");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array($column, $columns)) {
        echo "Adding column '$column'...\n";
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
        echo "Column added successfully.\n";
    } else {
        echo "Column already exists.\n";
    }
}

try {
    addColumnIfNotExists($pdo, 'facturas', 'fecha_confirmacion', 'DATETIME NULL');
    addColumnIfNotExists($pdo, 'facturas', 'fecha_pago', 'DATETIME NULL');
    
    echo "Migration completed.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
