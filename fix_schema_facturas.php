<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = db_connect('write');
    // Check if column exists
    $stmt = $db->query("SHOW COLUMNS FROM facturas LIKE 'fecha_vencimiento'");
    
    if ($stmt->fetch()) {
        echo "✅ Column 'fecha_vencimiento' ALREADY exists.\n";
    } else {
        echo "🔧 Adding 'fecha_vencimiento'...\n";
        $db->exec("ALTER TABLE facturas ADD COLUMN fecha_vencimiento DATE NULL AFTER fecha_emision");
        echo "✅ Column added successfully.\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
