<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Admin\Dashboard::index');
$routes->get('dashboard', 'Admin\Dashboard::index');
$routes->get('data_kendala', 'Admin\Data_kendala::data_kendala');
$routes->get('admin/qr', 'Admin\Qr::index');


// Data chart dashboard (JSON)
$routes->get('dashboard/chart-data', 'Dashboard::chartData');

// Notifikasi (SSE + fallback)
$routes->get('notifications/latest', 'Notifications::latest');   // AJAX biasa
$routes->get('notifications/stream', 'Notifications::stream');   // SSE (real-time)


$routes->get('login', 'Admin\Auth::login');
