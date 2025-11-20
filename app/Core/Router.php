<?php
namespace App\Core;

class Router
{
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $path = '/' . trim($path, '/');
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $requestUri, string $method): void
    {
        $method = strtoupper($method);

        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        // Quitar el basePath si aplica
        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        if ($path === '') {
            $path = '/';
        }

        $path = rtrim($path, '/') ?: '/';

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo "<h1>404</h1><p>Ruta no encontrada: " . htmlspecialchars($path) . "</p>";
            return;
        }

        // Formato: 'NombreController@metodo'
        if (!str_contains($handler, '@')) {
            throw new \RuntimeException("Handler inválido para ruta {$path}");
        }

        [$controllerName, $action] = explode('@', $handler, 2);

        $fqcn = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($fqcn)) {
            throw new \RuntimeException("Controlador {$fqcn} no encontrado");
        }

        $controller = new $fqcn();

        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Método {$action} no existe en {$fqcn}");
        }

        $controller->$action();
    }
}
