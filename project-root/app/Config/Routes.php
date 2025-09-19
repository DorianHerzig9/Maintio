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
    $routes->post('(:num)/components/(:num)/status', 'WorkOrders::updateComponentStatus/$1/$2');
});

// API Routes für AJAX
$routes->get('/api/work-orders/search', 'WorkOrders::search');

// Assets Routes
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

// Preventive Maintenance Routes
$routes->group('preventive-maintenance', function($routes) {
    $routes->get('', 'PreventiveMaintenance::index');
    $routes->get('create', 'PreventiveMaintenance::create');
    $routes->post('', 'PreventiveMaintenance::store');
    $routes->get('(:num)', 'PreventiveMaintenance::show/$1');
    $routes->get('(:num)/edit', 'PreventiveMaintenance::edit/$1');
    $routes->put('(:num)', 'PreventiveMaintenance::update/$1');
    $routes->post('(:num)', 'PreventiveMaintenance::update/$1');
    $routes->delete('(:num)', 'PreventiveMaintenance::delete/$1');
    $routes->post('(:num)/complete', 'PreventiveMaintenance::markCompleted/$1');
    $routes->post('generate-work-orders', 'PreventiveMaintenance::generateWorkOrders');
    $routes->get('dashboard-widget', 'PreventiveMaintenance::dashboardWidget');
});

// API Routes für Preventive Maintenance
$routes->group('api/preventive-maintenance', function($routes) {
    $routes->get('overdue', 'PreventiveMaintenance::getOverdue');
    $routes->get('upcoming/(:num)', 'PreventiveMaintenance::getUpcoming/$1');
    $routes->get('stats', 'PreventiveMaintenance::getStats');
});

// Users Routes
$routes->group('users', function($routes) {
    $routes->get('', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('', 'Users::store');
    $routes->get('(:num)', 'Users::show/$1');
    $routes->get('(:num)/edit', 'Users::edit/$1');
    $routes->put('(:num)', 'Users::update/$1');
    $routes->post('(:num)', 'Users::update/$1'); // Für Formulare ohne PUT-Support
    $routes->delete('(:num)', 'Users::delete/$1');
    $routes->post('(:num)/toggle-status', 'Users::toggleStatus/$1');
});

// Reports Routes
$routes->group('reports', function($routes) {
    $routes->get('', 'Reports::index');
    $routes->get('work-orders', 'Reports::workOrders');
    $routes->get('assets', 'Reports::assets');
    $routes->get('maintenance', 'Reports::maintenance');
    $routes->get('performance', 'Reports::performance');
    $routes->get('custom', 'Reports::custom');
    $routes->post('generate-custom', 'Reports::generateCustom');
    $routes->get('scheduled', 'Reports::scheduled');
    $routes->post('create-scheduled', 'Reports::createScheduled');
    $routes->get('export-work-orders', 'Reports::exportWorkOrders');
    $routes->get('export-assets', 'Reports::exportAssets');
    $routes->get('export-custom', 'Reports::exportCustom');
});

// Settings Routes
$routes->group('settings', function($routes) {
    $routes->get('', 'Settings::index');
    $routes->post('', 'Settings::update');
    $routes->get('profile', 'Settings::profile');
    $routes->get('backup', 'Settings::backup');
    $routes->get('logs', 'Settings::logs');
});

// Fallback
$routes->get('/home', 'Home::index');
