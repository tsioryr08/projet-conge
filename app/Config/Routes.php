<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ─── Routes publiques ────────────────────────────────────────────────────────
$routes->get('/',      'AuthController::login');
$routes->get('login',  'AuthController::login');
$routes->post('login', 'AuthController::authenticate');
$routes->get('logout', 'AuthController::logout');

// ─── Routes Employé ──────────────────────────────────────────────────────────
$routes->group('employe', ['filter' => 'auth:employe'], function ($routes) {
    $routes->get('/',                        'Employe\Dashboard::index');
    $routes->get('demandes',                 'Employe\Demandes::index');
    $routes->get('demandes/create',          'Employe\Demandes::create');
    $routes->post('demandes',                'Employe\Demandes::store');
    $routes->post('demandes/(:num)/annuler', 'Employe\Demandes::cancel/$1');
});

// ─── Routes RH ───────────────────────────────────────────────────────────────
$routes->group('rh', ['filter' => 'auth:rh'], function ($routes) {
    $routes->get('/',                          'Rh\DemandController::index');
    $routes->get('demandes',                   'Rh\DemandController::index');
    $routes->get('demandes/(:num)',             'Rh\DemandController::detail/$1');
    $routes->post('demandes/(:num)/approuver', 'Rh\DemandController::approve/$1');
    $routes->post('demandes/(:num)/refuser',   'Rh\DemandController::refuse/$1');
    $routes->get('soldes',                     'Rh\SoldeController::index');
});

// ─── Routes Admin ─────────────────────────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
});