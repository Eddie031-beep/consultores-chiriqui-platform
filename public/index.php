<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Punto de entrada principal de la aplicación
 * Consultores Chiriquí Platform
 */

// Cargar configuraciones
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';

// Iniciar sesión
session_start();

// Autoloader de clases
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

// Cargar y ejecutar router
try {
    require_once __DIR__ . '/../config/routes.php';
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    die("Error: " . $e->getMessage());
}