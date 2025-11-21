<?php
/**
 * RUTAS SIMPLIFICADAS - Sistema Unificado de Autenticación
 */

$routes = [
    // ============ GET ROUTES ============
    'GET' => [
        // Home
        '/' => 'HomeController@index',
        
        // Autenticación Unificada
        '/auth' => 'AuthController@index',                    // Modal de selección
        '/auth/login' => 'AuthController@showLogin',          // Login unificado con ?tipo=
        '/auth/registro' => 'AuthController@showRegistro',    // Registro unificado con ?tipo=
        '/logout' => 'AuthController@logout',
        
        // Vacantes públicas
        '/vacantes' => 'VacanteController@listar',
        '/vacantes/(?P<slug>[\w-]+)' => 'VacanteController@detalle',
        '/postular/(?P<vacante_id>\d+)' => 'VacanteController@prePostular',
        
        // Dashboard Candidato
        '/candidato/dashboard' => 'CandidatoController@dashboard',
        '/candidato/postulaciones' => 'CandidatoController@postulaciones',
        '/candidato/perfil' => 'CandidatoController@perfil',
        '/candidato/postular/(?P<vacante_id>\d+)' => 'CandidatoController@postular', // Permitir GET para procesar postulación tras redirección
        '/candidato/opciones-perfil' => 'CandidatoController@opcionesPerfil',
        '/candidato/perfil-manual' => 'CandidatoController@perfilManual',

        // Dashboard Empresa
        '/empresa/dashboard' => 'EmpresaController@dashboard',
        '/empresa/vacantes' => 'EmpresaController@vacantes',
        '/empresa/vacantes/crear' => 'EmpresaController@crearVacante',
        '/empresa/vacantes/(?P<id>\d+)' => 'EmpresaController@editarVacante',
        '/empresa/candidatos' => 'EmpresaController@candidatos',
        '/empresa/facturacion' => 'EmpresaController@facturacion',
        
        // Dashboard Consultora
        '/consultora/dashboard' => 'ConsultoraController@dashboard',
        '/consultora/empresas' => 'ConsultoraController@empresas',
        '/consultora/empresas/crear' => 'ConsultoraController@crearEmpresa',
        '/consultora/empresas/(?P<id>\d+)/editar' => 'ConsultoraController@editarEmpresa',
        '/consultora/contratos/(?P<id>\d+)' => 'ConsultoraController@verContrato',
        '/consultora/facturacion' => 'FacturacionController@listar',
        '/consultora/facturacion/generar' => 'FacturacionController@generar',
        '/consultora/facturacion/ver/(?P<id>\d+)' => 'FacturacionController@ver',
        '/consultora/info' => 'ConsultoraController@info',
        
        // Chatbot
        '/chatbot' => 'ChatbotController@chat',
    ],

    // ============ POST ROUTES ============
    'POST' => [
        // Autenticación unificada
        '/auth/login' => 'AuthController@processLogin',
        '/auth/registro' => 'AuthController@processRegistro',
        
        // Candidato
        '/candidato/postular/(?P<vacante_id>\d+)' => 'CandidatoController@postular',
        '/candidato/perfil' => 'CandidatoController@perfil',
        '/candidato/subir-cv' => 'CandidatoController@subirCV',
        '/candidato/guardar-manual' => 'CandidatoController@guardarManual',
        
        // Empresa
        '/empresa/vacantes/crear' => 'EmpresaController@storeVacante',
        '/empresa/vacantes/(?P<id>\d+)' => 'EmpresaController@updateVacante',

        // Consultora
        '/consultora/empresas/crear' => 'ConsultoraController@storeEmpresa',
        '/consultora/empresas/(?P<id>\d+)/editar' => 'ConsultoraController@updateEmpresa',
        '/consultora/facturacion/generar' => 'FacturacionController@procesarFactura',
        
        // Chatbot
        '/chatbot' => 'ChatbotController@chat',
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
        $this->show404();
    }

    private function matches($route, $uri, &$params = [])
    {
        $pattern = str_replace('/', '\\/', $route);
        $pattern = preg_replace_callback(
            '/\(\?P<([a-zA-Z_]+)>[^)]+\)/',
            fn($m) => "(?P<{$m[1]}>[^/]+)",
            $pattern
        );
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
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
            $this->show500("Controlador '$controllerName' no encontrado");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            $this->show500("Método '$method' no encontrado en $controllerName");
            return;
        }

        // Ejecutar método con parámetros
        if (!empty($params)) {
            call_user_func_array([$controller, $method], array_values($params));
        } else {
            $controller->$method();
        }
    }

    private function show404()
    {
        header("HTTP/1.1 404 Not Found");
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Página no encontrada</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
                .container { background: white; padding: 40px; border-radius: 10px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #333; font-size: 4em; margin: 0; }
                p { color: #666; margin: 20px 0; }
                a { color: #667eea; text-decoration: none; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>404</h1>
                <p>La página que buscas no existe</p>
                <p><strong>Ruta:</strong> {$this->uri}</p>
                <a href='" . ENV_APP['BASE_URL'] . "'>← Volver al inicio</a>
            </div>
        </body>
        </html>";
        exit;
    }

    private function show500($message)
    {
        header("HTTP/1.1 500 Internal Server Error");
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>500 - Error del servidor</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
                .container { background: white; padding: 40px; border-radius: 10px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #e74c3c; font-size: 4em; margin: 0; }
                p { color: #666; margin: 20px 0; }
                .error { background: #fee; padding: 15px; border-left: 4px solid #e74c3c; margin: 20px 0; text-align: left; }
                a { color: #667eea; text-decoration: none; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>500</h1>
                <p>Error interno del servidor</p>
                <div class='error'>" . htmlspecialchars($message) . "</div>
                <a href='" . ENV_APP['BASE_URL'] . "'>← Volver al inicio</a>
            </div>
        </body>
        </html>";
        exit;
    }
}

// ============ EJECUTAR ROUTER ============
(new SimpleRouter($routes))->dispatch();