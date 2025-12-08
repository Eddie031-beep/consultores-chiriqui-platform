<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');

try {
    $stmt = $db->query("SHOW COLUMNS FROM interacciones_vacante LIKE 'tipo_interaccion'");
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo $e->getMessage();
}
