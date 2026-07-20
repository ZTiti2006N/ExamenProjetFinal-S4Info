<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Gestion des opérateurs (Configuration des préfixes)
$routes->group('operators', function ($routes) {
    $routes->get('/',               'Operators::index');
    $routes->get('create',          'Operators::create');
    $routes->post('store',          'Operators::store');
    $routes->get('edit/(:num)',     'Operators::edit/$1');
    $routes->post('update/(:num)',  'Operators::update/$1');
    $routes->get('delete/(:num)',   'Operators::delete/$1');
});
