<?php
// Define constants to avoid path issues if possible, or just use absolute
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'consultores_db'); // Guessing name or checking env. 
// actually I should use the one from known config or just db_connect if I can fix include.
// Let's rely on the framework file with absolute path.

require_once 'c:\Proyectos\ExamenFinalDS4\consultores-chiriqui-platform\app\Config\Database.php';

$db = db_connect('local');

echo "Actualizando tabla interacciones_vacante...\n";

$queries = [
    "ALTER TABLE interacciones_vacante ADD COLUMN session_id VARCHAR(100) NULL AFTER ip",
    "ALTER TABLE interacciones_vacante MODIFY COLUMN user_id INT UNSIGNED NULL",
    "ALTER TABLE interacciones_vacante ADD INDEX idx_session_date (session_id, fecha_hora)"
];

foreach ($queries as $q) {
    try {
        $db->query($q);
        echo "Ejecutado: $q\n";
    } catch (PDOException $e) {
        echo "Info: " . $e->getMessage() . "\n";
    }
}

echo "Listo.\n";
