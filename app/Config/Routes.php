<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');

// Data chart dashboard (JSON)
$routes->get('dashboard/chart-data', 'Dashboard::chartData');

// Notifikasi (SSE + fallback)
$routes->get('notifications/latest', 'Notifications::latest');   // AJAX biasa
$routes->get('notifications/stream', 'Notifications::stream');   // SSE (real-time)
