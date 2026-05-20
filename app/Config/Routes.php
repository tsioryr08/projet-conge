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

    // Routes ajoutées dans HEAD
    $routes->get('profile', 'Employe\Profil::edit');
    $routes->post('profile', 'Employe\Profil::update');
});

// ─── Routes RH ───────────────────────────────────────────────────────────────
$routes->group('rh', ['filter' => 'auth:rh'], function ($routes) {
    $routes->get('/',                           'Rh\DemandController::index');
    $routes->get('demandes',                    'Rh\DemandController::index');
    $routes->get('demandes/(:num)',             'Rh\DemandController::detail/$1');
    $routes->post('demandes/(:num)/approuver',  'Rh\DemandController::approve/$1');
    $routes->post('demandes/(:num)/refuser',    'Rh\DemandController::refuse/$1');
    $routes->get('soldes',                      'Rh\SoldeController::index');
});

// ─── Routes Admin ─────────────────────────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    // Dashboard
    $routes->get('/',                                   'Admin\DashboardController::index');
    
    // Employés
    $routes->get('employes',                           'Admin\EmployeController::index');
    $routes->get('employes/create',                    'Admin\EmployeController::create');
    $routes->post('employes',                          'Admin\EmployeController::store');
    $routes->get('employes/(:num)/edit',               'Admin\EmployeController::edit/$1');
    $routes->post('employes/(:num)',                   'Admin\EmployeController::update/$1');
    $routes->post('employes/(:num)/deactivate',        'Admin\EmployeController::deactivate/$1');
    
    // Départements
    $routes->get('departements',                       'Admin\DepartementController::index');
    $routes->get('departements/create',                'Admin\DepartementController::create');
    $routes->post('departements',                      'Admin\DepartementController::store');
    $routes->get('departements/(:num)/edit',           'Admin\DepartementController::edit/$1');
    $routes->post('departements/(:num)',               'Admin\DepartementController::update/$1');
    $routes->delete('departements/(:num)',             'Admin\DepartementController::delete/$1');
    
    // Types de congé
    $routes->get('types_conge',                        'Admin\TypeCongeController::index');
    $routes->get('types_conge/create',                 'Admin\TypeCongeController::create');
    $routes->post('types_conge',                       'Admin\TypeCongeController::store');
    $routes->get('types_conge/(:num)/edit',            'Admin\TypeCongeController::edit/$1');
    $routes->post('types_conge/(:num)',                'Admin\TypeCongeController::update/$1');
    $routes->delete('types_conge/(:num)',              'Admin\TypeCongeController::delete/$1');
});