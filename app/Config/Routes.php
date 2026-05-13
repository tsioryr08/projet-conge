<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Routes publiques
$routes->get('/', 'Employe\\Auth::login');
$routes->get('login', 'Employe\\Auth::login');
$routes->post('login', 'Employe\\Auth::attempt');
$routes->get('logout', 'Employe\\Auth::logout');

// Routes Employé protégées
$routes->group('employe', ['filter' => 'auth:employe'], function($routes) {
    $routes->get('/', 'Employe\\Dashboard::index');
    $routes->get('demandes', 'Employe\\Demandes::index');
    $routes->get('demandes/create', 'Employe\\Demandes::create');
    $routes->post('demandes', 'Employe\\Demandes::store');
    $routes->post('demandes/(:num)/annuler', 'Employe\\Demandes::cancel/$1');
});
