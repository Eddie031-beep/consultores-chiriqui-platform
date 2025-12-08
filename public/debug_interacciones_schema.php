<?php
require_once __DIR__ . '/../app/Config/Database.php';

try {
    $db = db_connect('local');
    $stmt = $db->query("DESCRIBE interacciones_vacante");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columns in interacciones_vacante:\n";
    foreach($cols as $col) {
        echo $col['Field'] . " - " . $col['Type'] . " - Null: " . $col['Null'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
