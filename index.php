<?php
ob_start();

define('BASE_PATH', __DIR__);

spl_autoload_register(function (string $class): void {
    $file = BASE_PATH . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$appConfig = require BASE_PATH . '/app/Config/app.php';
$router = new App\Core\Router($appConfig);

$router->get('/', 'App\Controllers\Public\HomeController', 'index');
$router->get('/pengaduan', 'App\Controllers\Public\PengaduanController', 'create');
$router->post('/pengaduan', 'App\Controllers\Public\PengaduanController', 'store');
$router->get('/pengaduan/tracking', 'App\Controllers\Public\PengaduanController', 'tracking');
$router->post('/pengaduan/tracking', 'App\Controllers\Public\PengaduanController', 'cekStatus');
$router->get('/pengaduan/{kode}', 'App\Controllers\Public\PengaduanController', 'show');
$router->get('/saran', 'App\Controllers\Public\SaranController', 'create');
$router->post('/saran', 'App\Controllers\Public\SaranController', 'store');

$router->get('/api/kelurahan/{id}', 'App\Controllers\Public\PengaduanController', 'getKelurahan');

$router->get('/admin/login', 'App\Controllers\Admin\AuthController', 'loginForm');
$router->post('/admin/login', 'App\Controllers\Admin\AuthController', 'login');
$router->get('/admin/logout', 'App\Controllers\Admin\AuthController', 'logout');
$router->get('/admin/dashboard', 'App\Controllers\Admin\DashboardController', 'index');

$router->get('/admin/pengaduan', 'App\Controllers\Admin\PengaduanController', 'index');
$router->get('/admin/pengaduan/export', 'App\Controllers\Admin\PengaduanController', 'exportExcel');
$router->get('/admin/pengaduan/{id}', 'App\Controllers\Admin\PengaduanController', 'detail');
$router->post('/admin/pengaduan/{id}/tanggapan', 'App\Controllers\Admin\PengaduanController', 'storeTanggapan');

$router->get('/admin/kecamatan', 'App\Controllers\Admin\KecamatanController', 'index');
$router->get('/admin/kecamatan/create', 'App\Controllers\Admin\KecamatanController', 'create');
$router->post('/admin/kecamatan', 'App\Controllers\Admin\KecamatanController', 'store');
$router->get('/admin/kecamatan/{id}/edit', 'App\Controllers\Admin\KecamatanController', 'edit');
$router->post('/admin/kecamatan/{id}', 'App\Controllers\Admin\KecamatanController', 'update');
$router->post('/admin/kecamatan/{id}/delete', 'App\Controllers\Admin\KecamatanController', 'destroy');

$router->get('/admin/kelurahan', 'App\Controllers\Admin\KelurahanController', 'index');
$router->get('/admin/kelurahan/create', 'App\Controllers\Admin\KelurahanController', 'create');
$router->post('/admin/kelurahan', 'App\Controllers\Admin\KelurahanController', 'store');
$router->get('/admin/kelurahan/{id}/edit', 'App\Controllers\Admin\KelurahanController', 'edit');
$router->post('/admin/kelurahan/{id}', 'App\Controllers\Admin\KelurahanController', 'update');
$router->post('/admin/kelurahan/{id}/delete', 'App\Controllers\Admin\KelurahanController', 'destroy');

$router->get('/admin/log-aktivitas', 'App\Controllers\Admin\LogAktivitasController', 'index');
$router->get('/admin/log-aktivitas/export', 'App\Controllers\Admin\LogAktivitasController', 'exportCsv');

$router->get('/admin/query-recommend', 'App\Controllers\Admin\QueryRecommendController', 'index');

$router->get('/admin/saran', 'App\Controllers\Admin\SaranController', 'index');
$router->post('/admin/saran/{id}/delete', 'App\Controllers\Admin\SaranController', 'destroy');

$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);
