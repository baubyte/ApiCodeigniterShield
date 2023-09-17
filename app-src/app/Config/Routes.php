<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes){
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->get('profile', 'AuthController::profile',['filter' => 'authapi']);
    $routes->post('logout', 'AuthController::logout',['filter' => 'authapi']);
    $routes->get('invalid', 'AuthController::invalidRequest');

    $routes->post('create-project', 'ProjectController::create');
    $routes->get('list-projects', 'ProjectController::index');
    $routes->delete('delete-project/(:any)', 'ProjectController::delete');
});
