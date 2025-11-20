<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

try {
    // Conectamos a la réplica (Ubuntu)
    $pdo = db_connect('replica');

    $stmt = $pdo->query("SELECT COUNT(*) AS total_vacantes FROM vacantes");
    $row = $stmt->fetch();

    echo "<h1>Conexión a réplica OK ✅</h1>";
    echo "<p>Total de vacantes en la réplica: <strong>" . (int)$row['total_vacantes'] . "</strong></p>";
} catch (Throwable $e) {
    echo "<h1>Error conectando a la réplica ❌</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
