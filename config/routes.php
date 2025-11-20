<?php
// config/routes.php

use App\Core\Router;

/** @var Router $router */

// Landing principal (público)
$router->get('/', 'DashboardController@home');

// Información de la consultora (público)
$router->get('/consultora/info', 'DashboardController@infoConsultora');

// Login consultora
$router->get('/login/consultora', 'AuthController@showLoginConsultora');
$router->post('/login/consultora', 'AuthController@loginConsultora');

// Login empresa
$router->get('/login/empresa', 'AuthController@showLoginEmpresa');
$router->post('/login/empresa', 'AuthController@loginEmpresa');

// Logout (común)
$router->get('/logout', 'AuthController@logout');

// Dashboards
$router->get('/consultora/dashboard', 'DashboardController@consultora');
$router->get('/empresa/dashboard', 'DashboardController@empresa');

// ========================
// Rutas EMPRESAS (consultora)
// ========================
$router->get('/consultora/empresas', 'EmpresaController@index');
$router->get('/consultora/empresas/nueva', 'EmpresaController@create');
$router->post('/consultora/empresas/nueva', 'EmpresaController@store');
$router->get('/consultora/empresas/editar', 'EmpresaController@edit');
$router->post('/consultora/empresas/editar', 'EmpresaController@update');
$router->get('/consultora/empresas/crear-usuario', 'EmpresaController@createUser');
$router->post('/consultora/empresas/crear-usuario', 'EmpresaController@storeUser');

// ========================
// Rutas VACANTES (empresa)
// ========================
$router->get('/empresa/vacantes', 'VacanteController@index');
$router->get('/empresa/vacantes/nueva', 'VacanteController@create');
$router->post('/empresa/vacantes/nueva', 'VacanteController@store');
$router->get('/empresa/vacantes/editar', 'VacanteController@edit');
$router->post('/empresa/vacantes/editar', 'VacanteController@update');
$router->post('/empresa/vacantes/cerrar', 'VacanteController@close');

// ========================
// Rutas CHATBOT (público)
// ========================
$router->get('/chatbot', 'ChatbotController@widget');
$router->post('/chatbot/api', 'ChatbotController@api');
$router->post('/chatbot/interaccion', 'ChatbotController@registrarInteraccion');

// ========================
// Rutas FACTURACIÓN (consultora)
// ========================
$router->get('/consultora/facturacion', 'FacturacionController@index');
$router->get('/consultora/facturacion/estadisticas', 'FacturacionController@estadisticas');
$router->get('/consultora/facturacion/generar', 'FacturacionController@generar');
$router->post('/consultora/facturacion/crear', 'FacturacionController@crear');
$router->get('/consultora/facturacion/ver', 'FacturacionController@ver');