<?php
// HARDCODED DB CONNECTION
$host = 'localhost';
$db   = 'consultores_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected successfully to $db\n";
    
    $stmts = [
        "ALTER TABLE facturas ADD COLUMN cufe VARCHAR(100) NULL AFTER token_publico",
        "ALTER TABLE facturas ADD COLUMN protocolo_autorizacion VARCHAR(50) NULL AFTER cufe",
        "ALTER TABLE facturas ADD COLUMN clave_acceso VARCHAR(100) NULL AFTER protocolo_autorizacion",
        "ALTER TABLE facturas ADD COLUMN fecha_autorizacion DATETIME NULL AFTER clave_acceso",
        "ALTER TABLE facturas MODIFY COLUMN estado ENUM('emitida','pagada','anulada','en_revision') DEFAULT 'emitida'"
    ];

    foreach ($stmts as $sql) {
        try {
            $pdo->query($sql);
            echo "SUCCESS: $sql\n";
        } catch (PDOException $e) {
            echo "SKIPPED/ERROR (likely already exists): " . $e->getMessage() . "\n";
        }
    }

} catch (\PDOException $e) {
    echo "CRITICAL DB CONNECTION ERROR: " . $e->getMessage();
}
