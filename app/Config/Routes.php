<?php

use CodeIgniter\Router\RouteCollection;

// Public Portal Routes
$routes->get('/', '\App\Controllers\PublicPortalController::index');
$routes->get('profil', '\App\Controllers\PublicPortalController::profile');
$routes->get('berita', '\App\Controllers\PublicPortalController::news');
$routes->get('program', '\App\Controllers\PublicPortalController::programs');
$routes->get('donasi', '\App\Controllers\PublicPortalController::donation');
$routes->get('kontak', '\App\Controllers\PublicPortalController::contact');

// Load Domain Masjid Routes
if (file_exists(APPPATH . 'Domains/Masjid/Routes/masjid.php')) {
    require APPPATH . 'Domains/Masjid/Routes/masjid.php';
}

// Load Domain Jamaah Routes
if (file_exists(APPPATH . 'Domains/Jamaah/Routes/jamaah.php')) {
    require APPPATH . 'Domains/Jamaah/Routes/jamaah.php';
}

// Load Domain Family Routes
if (file_exists(APPPATH . 'Domains/Family/Routes/family.php')) {
    require APPPATH . 'Domains/Family/Routes/family.php';
}

// Load Domain Financial Routes
if (file_exists(APPPATH . 'Domains/Financial/Routes/financial.php')) {
    require APPPATH . 'Domains/Financial/Routes/financial.php';
}

// Admin Routes
$routes->get('admin/dashboard', '\App\Controllers\AdminDashboardController::index');
$routes->get('admin/master', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/financial', '\App\Controllers\AdminFinancialWorkspaceController::index');
$routes->get('admin/financial/create', '\App\Controllers\AdminFinancialWorkspaceController::create');
$routes->get('admin/financial/detail/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::detail/$1');
$routes->get('admin/reporting', '\App\Controllers\AdminReportingWorkspaceController::index');
$routes->get('admin/reporting/preview', '\App\Controllers\AdminReportingWorkspaceController::preview');

// Web Installer Routes
$routes->match(['get', 'post'], 'install', '\App\Controllers\InstallerController::welcome');
$routes->match(['get', 'post'], 'install/requirements', '\App\Controllers\InstallerController::requirements');
$routes->match(['get', 'post'], 'install/database', '\App\Controllers\InstallerController::database');
$routes->match(['get', 'post'], 'install/application', '\App\Controllers\InstallerController::application');
$routes->match(['get', 'post'], 'install/admin', '\App\Controllers\InstallerController::admin');
$routes->match(['get', 'post'], 'install/finish', '\App\Controllers\InstallerController::finish');

