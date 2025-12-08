<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');
try {
    $stmt = $db->query("SELECT id, titulo, slug, estado FROM vacantes");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo $e->getMessage();
}
