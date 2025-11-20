<?php
// config/routes.php

/** @var \App\Core\Router $router */

// Ruta principal (landing / dashboard público)
$router->get('/', 'DashboardController@home');

// Más adelante agregaremos:
// $router->get('/login/empresa', 'AuthController@showLoginEmpresa');
// $router->post('/login/empresa', 'AuthController@loginEmpresa');
// etc.
