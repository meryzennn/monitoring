<?php

namespace Config;

use CodeIgniter\Config\Services;

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');   // tidak dipakai karena '/' di-override di bawah
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);            // keamanan: nonaktifkan legacy auto routing

/*
 * --------------------------------------------------------------------
 * Load the system's routing file first, so that the app and ENVIRONMENT
 * can override as needed.
 * --------------------------------------------------------------------
 */
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * ROUTES APLIKASI
 * --------------------------------------------------------------------
 */

/* =========================
 * AUTH (PUBLIC)
 * ========================= */
$routes->get('login',    'Admin\Auth::login', ['as' => 'auth.login']);
$routes->post('auth/do', 'Admin\Auth::do',    ['as' => 'auth.do']);

// fallback: jika user buka GET /auth/do → arahkan ke /login
$routes->get('auth/do', static function () {
    return redirect()->to(site_url('login'));
});

$routes->get('logout',   'Admin\Auth::logout', ['as' => 'auth.logout']);


/* =========================
 * TEKNISI via TOKEN (PUBLIC)
 * (akses tanpa login melalui QR)
 * ========================= */
$routes->group('', static function ($routes) {
    // Detail perangkat berdasarkan token (token disimpan di ac_units.kode_qr)
    $routes->get('ac/(:segment)',           'Teknisi\Page::detailByToken/$1',    ['as' => 'teknisi.ac.detail']);

    // Halaman/form perbaikan untuk token tsb
    $routes->get('ac/(:segment)/perbaikan', 'Teknisi\Page::perbaikanByToken/$1', ['as' => 'teknisi.ac.repair']);

    // Kompatibilitas lama: /teknisi/perbaikan?t=TOKEN → redirect ke /ac/{TOKEN}/perbaikan
    $routes->get('teknisi/perbaikan', static function () {
        $t = service('request')->getGet('t');
        return $t
            ? redirect()->to(site_url('ac/' . rawurlencode($t) . '/perbaikan'))
            : redirect()->to(site_url('/'));
    });
});


/* =========================
 * AREA TERPROTEKSI (WAJIB LOGIN)
 * ========================= */
$routes->group('', ['filter' => 'auth'], static function ($routes) {

    // -------- ADMIN-ONLY --------
    $routes->group('', ['filter' => 'role:admin'], static function ($routes) {

        // Dashboard & beranda admin
        $routes->get('/',         'Admin\Dashboard::index', ['as' => 'admin.home']);
        $routes->get('dashboard', 'Admin\Dashboard::index', ['as' => 'admin.dashboard']);

        // Admin → QR (generator & simpan)
        $routes->get('admin/qr',       'Admin\Qr::index', ['as' => 'admin.qr']);
        $routes->post('admin/qr/save', 'Admin\Qr::save',  ['as' => 'admin.qr.save']); // simpan perangkat + foto

        // Menu lain (sesuaikan dengan controllermu)
        $routes->get('data_kendala', 'Admin\Data_kendala::data_kendala', ['as' => 'admin.data_kendala']);

        // Data chart dashboard (JSON)
        $routes->get('dashboard/chart-data', 'Admin\Dashboard::chartData', ['as' => 'admin.chart_data']);

        // Notifikasi (SSE + fallback) — sesuaikan namespace bila perlu
        $routes->get('notifications/latest', 'Admin\Notifications::latest', ['as' => 'admin.notif.latest']);
        $routes->get('notifications/stream', 'Admin\Notifications::stream', ['as' => 'admin.notif.stream']);

        // Data Pegawai (CRUD via modal / REST-like)
        $routes->get('pegawai',           'Admin\Employees::index',  ['as' => 'admin.emp.index']);
        $routes->get('pegawai/(:num)',    'Admin\Employees::show/$1');
        $routes->post('pegawai',          'Admin\Employees::store');
        $routes->put('pegawai/(:num)',    'Admin\Employees::update/$1');
        $routes->delete('pegawai/(:num)', 'Admin\Employees::delete/$1');
        $routes->get('pegawai/search',    'Admin\Employees::search');
    });

    // -------- USER/PEGAWAI (tambahkan bila diperlukan) --------
});


/*
 * --------------------------------------------------------------------
 * Tambahan routes per-environment
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
