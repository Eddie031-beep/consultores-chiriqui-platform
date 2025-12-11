<?php
// fix_db_v2.php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

try {
    echo "Conectando...\n";
    $db = db_connect('write');
    echo "Conectado. Verificando columna 'solicitante_id'...\n";

    $stmt = $db->query("SHOW COLUMNS FROM interacciones_vacante LIKE 'solicitante_id'");
    if ($stmt->fetch()) {
        echo "✅ La columna 'solicitante_id' YA existe.\n";
    } else {
        echo "🔧 Agregando columna 'solicitante_id'...\n";
        $db->exec("ALTER TABLE interacciones_vacante ADD COLUMN solicitante_id INT NULL AFTER session_id");
        echo "✅ Columna agregada exitosamente.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
