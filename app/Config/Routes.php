<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Rapports / Tableaux de bord
$routes->group('reports', function ($routes) {
    $routes->get('fees-summary',      'Reports::feesSummary');
    $routes->get('accounts-summary',  'Reports::accountsSummary');
    $routes->get('operator-payouts',  'Reports::operatorPayouts');
});

// Authentification automatique par téléphone
$routes->get('login', 'Auth::login');
$routes->post('auth/authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');

// Page d'accueil = Login (client)
$routes->get('/', 'Auth::login');

// Espace client (après connexion)
$routes->group('client', function ($routes) {
    $routes->get('/',              'Client::index');
    $routes->post('deposit',       'Client::deposit');
    $routes->post('withdrawal',    'Client::withdrawal');
    $routes->post('transfer',      'Client::transfer');
    $routes->get('history',        'Client::history');
    $routes->get('balance',        'Client::balance');
});

// Dashboard admin/opérateur
$routes->get('log_admin', 'Home::index');

// Gestion des opérateurs (Configuration des préfixes)
$routes->group('operators', function ($routes) {
    $routes->get('/',               'Operators::index');
    $routes->get('create',          'Operators::create');
    $routes->post('store',          'Operators::store');
    $routes->get('edit/(:num)',     'Operators::edit/$1');
    $routes->post('update/(:num)',  'Operators::update/$1');
    $routes->get('delete/(:num)',   'Operators::delete/$1');
});

// Gestion des types d'opérations
$routes->group('operation-types', function ($routes) {
    $routes->get('/',                   'OperationTypes::index');
    $routes->get('create',              'OperationTypes::create');
    $routes->post('store',              'OperationTypes::store');
    $routes->get('edit/(:num)',         'OperationTypes::edit/$1');
    $routes->post('update/(:num)',      'OperationTypes::update/$1');
    $routes->get('delete/(:num)',       'OperationTypes::delete/$1');
    $routes->get('detail/(:num)',       'OperationTypes::detail/$1');
    // Routes pour les barèmes de frais
    $routes->post('fee-scales/store/(:num)',   'FeeScales::store/$1');
    $routes->get('fee-scales/delete-all/(:num)','FeeScales::deleteAll/$1');
});