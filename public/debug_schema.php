<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');
try {
    $stmt = $db->query("DESCRIBE solicitantes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'genero') {
            echo "TYPE_START:" . $col['Type'] . ":TYPE_END";
        }
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}
