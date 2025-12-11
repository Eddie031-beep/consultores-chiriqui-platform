<?php
require_once __DIR__ . '/public/index.php'; // Bootloader simplificado o manual

// Manual connect ya que index.php puede tener output buffering o ejecutar router
require_once __DIR__ . '/config/database.php';

try {
    $db = db_connect('write');
    echo "Conectado a la BD.\n";

    // Verificar si existe la columna
    $stmt = $db->query("SHOW COLUMNS FROM interacciones_vacante LIKE 'solicitante_id'");
    if ($stmt->fetch()) {
        echo "La columna 'solicitante_id' ya existe.\n";
    } else {
        echo "Agregando columna 'solicitante_id'...\n";
        $db->exec("ALTER TABLE interacciones_vacante ADD COLUMN solicitante_id INT NULL AFTER session_id");
        echo "Columna agregada exitosamente.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
