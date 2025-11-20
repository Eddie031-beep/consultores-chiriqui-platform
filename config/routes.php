<?php

/**
 * Define las rutas de la aplicación
 * Formato: 'METODO' => ['ruta' => 'Controlador@metodo']
 */

$routes = [
    // ============ GET ROUTES ============
    'GET' => [
        '/' => 'HomeController@index',
        '/auth' => 'AuthController@index',
        '/auth/login-candidato' => 'AuthController@showLoginCandidato',
        '/auth/registro-candidato' => 'AuthController@showRegistroCandidato',
        '/auth/login-empresa' => 'AuthController@showLoginEmpresa',
        '/auth/registro-empresa' => 'AuthController@showRegistroEmpresa',
        '/auth/login-consultora' => 'AuthController@showLoginConsultora',
        '/logout' => 'AuthController@logout',
        '/vacantes' => 'VacanteController@listar',
        '/vacantes/(?P<slug>[\w-]+)' => 'VacanteController@detalle',
        '/postular/(?P<vacante_id>\d+)' => 'VacanteController@prePostular',
        '/candidato/dashboard' => 'CandidatoController@dashboard',
        '/candidato/postulaciones' => 'CandidatoController@postulaciones',
        '/candidato/perfil' => 'CandidatoController@perfil',
        '/empresa/dashboard' => 'EmpresaController@dashboard',
        '/empresa/vacantes' => 'EmpresaController@vacantes',
        '/empresa/vacantes/crear' => 'EmpresaController@crearVacante',
        '/empresa/vacantes/(?P<id>\d+)' => 'EmpresaController@editarVacante',
        '/empresa/candidatos' => 'EmpresaController@candidatos',
        '/consultora/dashboard' => 'DashboardController@consultoraDashboard',
        '/consultora/empresas' => 'EmpresaController@listarEmpresas',
        '/consultora/reportes' => 'DashboardController@reportes',
    ],

    // ============ POST ROUTES ============
    'POST' => [
        '/auth/login-candidato' => 'AuthController@loginCandidato',
        '/auth/registro-candidato' => 'AuthController@registroCandidato',
        '/auth/login-empresa' => 'AuthController@loginEmpresa',
        '/auth/registro-empresa' => 'AuthController@registroEmpresa',
        '/auth/login-consultora' => 'AuthController@loginConsultora',
        '/candidato/postular/(?P<vacante_id>\d+)' => 'CandidatoController@postular',
        '/candidato/perfil' => 'CandidatoController@perfil',
        '/empresa/vacantes/crear' => 'EmpresaController@crearVacante',
        '/empresa/vacantes/(?P<id>\d+)' => 'EmpresaController@editarVacante',
    ],

    // ============ CHAT BOT (GET Y POST) ============
    'GET|POST' => [
        '/chatbot/chat' => 'ChatbotController@chat',
    ],
];

// ============ SIMPLE ROUTER CLASS ============
class SimpleRouter
{
    private $routes;
    private $method;
    private $uri;

    public function __construct($routes)
    {
        $this->routes = $routes;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->getUri();
    }

    private function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover base path si existe
        $basePath = str_replace(['http://', 'https://', $_SERVER['HTTP_HOST']], '', ENV_APP['BASE_URL']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Normalizar URI
        $uri = rtrim($uri, '/') ?: '/';
        return $uri;
    }

    public function dispatch()
    {
        // Buscar ruta exacta o con patrón
        foreach ($this->routes as $methods => $routeList) {
            // Verificar si el método actual está en la lista
            $methodArray = explode('|', $methods);
            if (!in_array($this->method, $methodArray)) {
                continue;
            }

            foreach ($routeList as $route => $action) {
                $params = [];
                if ($this->matches($route, $this->uri, $params)) {
                    $this->executeAction($action, $params);
                    return;
                }
            }
        }

        // 404 - Ruta no encontrada
        header("HTTP/1.1 404 Not Found");
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Página no encontrada</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
                .container { background: white; padding: 40px; border-radius: 10px; max-width: 500px; margin: 0 auto; }
                h1 { color: #333; }
                p { color: #666; }
                a { color: #667eea; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>404 - Página no encontrada</h1>
                <p>La ruta <strong>{$this->uri}</strong> no existe.</p>
                <a href='" . ENV_APP['BASE_URL'] . "'>Volver al inicio</a>
            </div>
        </body>
        </html>";
        exit;
    }

    private function matches($route, $uri, &$params = [])
    {
        // Escapar slashes para regex
        $pattern = str_replace('/', '\\/', $route);
        
        // Convertir parámetros {id} a (?P<id>[^/]+)
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_]+)\}/',
            fn($m) => "(?P<{$m[1]}>[^/]+)",
            $pattern
        );

        // Crear patrón final
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            // Filtrar solo parámetros nombrados
            $params = array_filter($matches, fn($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    private function executeAction($action, $params)
    {
        [$controllerName, $method] = explode('@', $action);

        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            header("HTTP/1.1 500 Internal Server Error");
            echo "Error: Controlador '$controllerName' no encontrado en $controllerClass";
            exit;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            header("HTTP/1.1 500 Internal Server Error");
            echo "Error: Método '$method' no encontrado en $controllerName";
            exit;
        }

        // Ejecutar método con parámetros
        if (!empty($params)) {
            call_user_func_array([$controller, $method], array_values($params));
        } else {
            $controller->$method();
        }
    }
}

// ============ EJECUTAR ROUTER ============
(new SimpleRouter($routes))->dispatch();