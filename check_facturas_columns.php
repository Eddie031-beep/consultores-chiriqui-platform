<?php
require_once 'app/Core/Database.php';
require_once 'app/Helpers/Auth.php';
require_once 'config/env.php';
require_once 'config/database.php';

function db_connect($type = 'read') {
    $config = require 'config/database.php';
    $dbConfig = $config['connections']['mysql'];
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

try {
    $db = db_connect();
    $stmt = $db->query("DESCRIBE facturas");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in 'facturas':\n";
    foreach ($columns as $col) {
        echo "- $col\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
