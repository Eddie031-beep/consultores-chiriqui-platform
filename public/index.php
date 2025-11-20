<?php
// public/index.php
declare(strict_types=1);

// Autoloader súper simple para cargar clases de App\...
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
});

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';

use App\Core\Router;

// BASE_PATH debe coincidir con la ruta pública en Apache
$basePath = '/ExamenFinalDS4/consultores-chiriqui-platform/public';

$router = new Router($basePath);

// Cargar definición de rutas
require __DIR__ . '/../config/routes.php';

// Despachar la petición actual
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
