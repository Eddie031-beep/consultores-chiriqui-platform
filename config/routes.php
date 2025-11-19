<?php
// config/routes.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Prefijo donde vivirá el proyecto en Apache
$basePath = '/ExamenFinalDS4/consultores-chiriqui-platform/public';

if (str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}

if ($path === '' || $path === '/') {
    echo "<h1>Consultores Chiriquí Platform</h1>";
    echo "<p>Router funcionando 👌 (vista inicial provisional)</p>";
    exit;
}

http_response_code(404);
echo "<h1>404</h1><p>Ruta no encontrada: " . htmlspecialchars($path) . "</p>";
