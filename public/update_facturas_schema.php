<?php
require_once __DIR__ . '/../app/Config/Database.php';

$db = db_connect('local');

echo "Actualizando tabla facturas...\n";

$cols = [
    "ADD COLUMN cufe VARCHAR(100) NULL AFTER token_publico",
    "ADD COLUMN protocolo_autorizacion VARCHAR(50) NULL AFTER cufe",
    "ADD COLUMN clave_acceso VARCHAR(100) NULL AFTER protocolo_autorizacion",
    "ADD COLUMN fecha_autorizacion DATETIME NULL AFTER clave_acceso",
    "MODIFY COLUMN estado ENUM('emitida','pagada','anulada','en_revision') DEFAULT 'emitida'"
];

foreach ($cols as $col) {
    try {
        $db->query("ALTER TABLE facturas $col");
        echo "Exito: $col\n";
    } catch (PDOException $e) {
        echo "Info (probablemente ya existe): " . $e->getMessage() . "\n";
    }
}

echo "Listo.\n";
