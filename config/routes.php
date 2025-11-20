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
