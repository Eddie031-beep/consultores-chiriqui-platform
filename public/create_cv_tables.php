<?php
require __DIR__ . '/../config/database.php';
$db = db_connect('local');

try {
    // 1. Tabla de Experiencia
    $db->exec("CREATE TABLE IF NOT EXISTS candidato_experiencia (
        id INT AUTO_INCREMENT PRIMARY KEY,
        solicitante_id INT UNSIGNED NOT NULL,
        empresa VARCHAR(255) NOT NULL,
        puesto VARCHAR(255) NOT NULL,
        descripcion TEXT,
        fecha_inicio DATE,
        fecha_fin DATE,
        FOREIGN KEY (solicitante_id) REFERENCES solicitantes(id) ON DELETE CASCADE
    )");
    
    // 2. Tabla de Educación
    $db->exec("CREATE TABLE IF NOT EXISTS candidato_educacion (
        id INT AUTO_INCREMENT PRIMARY KEY,
        solicitante_id INT UNSIGNED NOT NULL,
        institucion VARCHAR(255) NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        nivel VARCHAR(100),
        fecha_graduacion DATE,
        FOREIGN KEY (solicitante_id) REFERENCES solicitantes(id) ON DELETE CASCADE
    )");
    
    // 3. Agregar columna 'habilidades' a solicitantes si no existe
    // (Opcional, pero util para 'Skills')
    $stmt = $db->query("SHOW COLUMNS FROM solicitantes LIKE 'habilidades'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE solicitantes ADD COLUMN habilidades TEXT DEFAULT NULL");
    }

    echo "Tablas creadas exitosamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
