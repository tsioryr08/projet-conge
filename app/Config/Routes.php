<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

// ─── Routes publiques ────────────────────────────────────────────────────────
$routes->get('/',      'Employe\Auth::login');
$routes->get('login',  'Employe\Auth::login');
$routes->post('login', 'Employe\Auth::attempt');   // ✅ corrigé
$routes->get('logout', 'Employe\Auth::logout');

// ─── Routes Employé ──────────────────────────────────────────────────────────
$routes->group('employe', ['filter' => 'auth:employe'], function ($routes) {
    $routes->get('/',                        'Employe\Dashboard::index');
    $routes->get('demandes',                 'Employe\Demandes::index');
    $routes->get('demandes/create',          'Employe\Demandes::create');
    $routes->post('demandes',                'Employe\Demandes::store');
    $routes->post('demandes/(:num)/annuler', 'Employe\Demandes::cancel/$1');
    $routes->get('profile',                  'Employe\Profil::edit');
    $routes->post('profile',                 'Employe\Profil::update');
    $routes->get('calendrier', 'Employe\Dashboard::calendrier');
    $routes->get('statistiques', 'Employe\Demandes::statistiques');
});

// ─── Routes RH ───────────────────────────────────────────────────────────────
$routes->group('rh', ['filter' => 'auth:rh'], function ($routes) {
    $routes->get('/',                          'Rh\DemandController::index');
    $routes->get('demandes',                   'Rh\DemandController::index');
    $routes->get('demandes/(:num)',            'Rh\DemandController::detail/$1');
    $routes->post('demandes/(:num)/approuver', 'Rh\DemandController::approve/$1');
    $routes->post('demandes/(:num)/refuser',   'Rh\DemandController::refuse/$1');
    $routes->get('soldes',                     'Rh\SoldeController::index');
});

// ─── Routes Admin ─────────────────────────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('/',                       'Admin\Dashboard::index');
    $routes->get('employes',                'Admin\Employes::index');
    $routes->get('employes/create',         'Admin\Employes::create');
    $routes->post('employes',               'Admin\Employes::store');
    $routes->get('employes/(:num)/edit',    'Admin\Employes::edit/$1');
    $routes->post('employes/(:num)/update', 'Admin\Employes::update/$1');
    $routes->post('employes/(:num)/toggle', 'Admin\Employes::toggle/$1');
});