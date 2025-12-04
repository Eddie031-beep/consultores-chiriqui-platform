<?php
/**
 * Punto de entrada principal
 */

// 1. Configuración de Errores (Solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Cargar configuraciones
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';

// 3. Iniciar sesión
session_start();

// 4. Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 5. Router con Captura de Errores Fatales
try {
    ob_start(); // Iniciar buffer de salida
    require_once __DIR__ . '/../config/routes.php';
    ob_end_flush(); // Enviar salida si todo sale bien
    
} catch (Throwable $e) {
    // Limpiamos cualquier salida parcial
    if (ob_get_length()) ob_end_clean(); 
    
    http_response_code(500);
    echo "<div style='font-family: sans-serif; background: #fee2e2; color: #991b1b; padding: 2rem; border: 2px solid #ef4444; margin: 2rem; border-radius: 10px;'>";
    echo "<h1 style='margin-top:0'>🛑 Error Fatal Detectado</h1>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> <span style='font-size: 1.5em; font-weight: bold;'>" . $e->getLine() . "</span></p>";
    echo "<hr style='border-color: #fca5a5;'>";
    echo "<h3>Traza del Error:</h3>";
    echo "<pre style='background: white; padding: 1rem; border-radius: 5px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
    exit;
}