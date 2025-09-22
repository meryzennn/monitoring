<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Admin\Dashboard::index');
$routes->get('dashboard', 'Admin\Dashboard::index');
$routes->get('data_kendala', 'Admin\Data_kendala::data_kendala');
$routes->get('admin/qr', 'Admin\Qr::index');

// Detail AC via token (sudah ada)
$routes->get('ac/(:segment)', 'Teknisi\Page::detailByToken/$1');

// Form perbaikan via token (baru)
$routes->get('ac/(:segment)/perbaikan', 'Teknisi\Page::perbaikanByToken/$1');

// (Opsional) kompatibilitas lama: /teknisi/perbaikan?t=TOKEN → redirect ke /ac/{TOKEN}/perbaikan
$routes->get('teknisi/perbaikan', static function () {
    $req = service('request');
    $t = $req->getGet('t');
    if ($t) return redirect()->to(site_url('ac/' . rawurlencode($t) . '/perbaikan'));
    // kalau tanpa token, arahkan ke beranda atau 404 sesuai kebutuhan
    return redirect()->to(site_url('/'));
});


// Data chart dashboard (JSON)
$routes->get('dashboard/chart-data', 'Dashboard::chartData');

// Notifikasi (SSE + fallback)
$routes->get('notifications/latest', 'Notifications::latest');   // AJAX biasa
$routes->get('notifications/stream', 'Notifications::stream');   // SSE (real-time)


$routes->get('login', 'Admin\Auth::login');
