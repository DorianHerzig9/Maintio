<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Dashboard (Hauptseite)
$routes->get('/', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/api/dashboard/stats', 'Dashboard::getStats');

// Work Orders Routes
$routes->group('work-orders', function($routes) {
    $routes->get('', 'WorkOrders::index');
    $routes->get('create', 'WorkOrders::create');
    $routes->post('', 'WorkOrders::store');
    $routes->get('(:num)', 'WorkOrders::show/$1');
    $routes->get('(:num)/edit', 'WorkOrders::edit/$1');
    $routes->put('(:num)', 'WorkOrders::update/$1');
    $routes->post('(:num)', 'WorkOrders::update/$1'); // Für Formulare ohne PUT-Support
    $routes->delete('(:num)', 'WorkOrders::delete/$1');
    $routes->post('(:num)/status', 'WorkOrders::updateStatus/$1');
});

// API Routes für AJAX
$routes->get('/api/work-orders/search', 'WorkOrders::search');

// Assets Routes (für später)
$routes->group('assets', function($routes) {
    $routes->get('', 'Assets::index');
    $routes->get('create', 'Assets::create');
    $routes->post('', 'Assets::store');
    $routes->get('(:num)', 'Assets::show/$1');
    $routes->get('(:num)/edit', 'Assets::edit/$1');
    $routes->put('(:num)', 'Assets::update/$1');
    $routes->post('(:num)', 'Assets::update/$1');
    $routes->delete('(:num)', 'Assets::delete/$1');
});

// Fallback
$routes->get('/home', 'Home::index');
