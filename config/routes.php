<?php
// config/routes.php

use App\Core\Router;

/** @var Router $router */

// Landing principal (público)
$router->get('/', 'DashboardController@home');

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
// Rutas módulo VACANTES (empresa)
// ========================
$router->get('/empresa/vacantes', 'VacanteController@index');
$router->get('/empresa/vacantes/nueva', 'VacanteController@create');
$router->post('/empresa/vacantes/nueva', 'VacanteController@store');
$router->get('/empresa/vacantes/editar', 'VacanteController@edit');
$router->post('/empresa/vacantes/editar', 'VacanteController@update');
$router->post('/empresa/vacantes/cerrar', 'VacanteController@close');
