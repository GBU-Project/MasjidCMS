<?php

use CodeIgniter\Router\RouteCollection;

// Public Portal Routes
$routes->get('/', '\App\Controllers\PublicPortalController::index');
$routes->get('profil', '\App\Controllers\PublicPortalController::profile');
$routes->get('berita', '\App\Controllers\PublicPortalController::news');
$routes->get('program', '\App\Controllers\PublicPortalController::programs');
$routes->get('donasi', '\App\Controllers\PublicPortalController::donation');
$routes->get('kontak', '\App\Controllers\PublicPortalController::contact');
$routes->get('galeri', '\App\Controllers\PublicPortalController::gallery');
$routes->get('transparansi', '\App\Controllers\PublicPortalController::transparency');

// Admin Workspace Routes
$routes->get('admin/dashboard', '\App\Controllers\AdminDashboardController::index');

// Master Data Workspace Routes
$routes->get('admin/master', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/master/create', '\App\Controllers\AdminMasterDataController::create');
$routes->post('admin/master/store', '\App\Controllers\AdminMasterDataController::store');
$routes->get('admin/master/delete/(:segment)/(:segment)', '\App\Controllers\AdminMasterDataController::delete/$1/$2');
$routes->get('admin/masjid', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/jamaah', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/family', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/users', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/rbac', '\App\Controllers\AdminMasterDataController::index');

// Financial Workspace Routes
$routes->get('admin/financial', '\App\Controllers\AdminFinancialWorkspaceController::index');
$routes->get('admin/financial/create', '\App\Controllers\AdminFinancialWorkspaceController::create');
$routes->post('admin/financial/store', '\App\Controllers\AdminFinancialWorkspaceController::store');
$routes->get('admin/financial/detail/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::detail/$1');
$routes->post('admin/financial/coa/store', '\App\Controllers\AdminFinancialWorkspaceController::storeCoa');
$routes->post('admin/financial/budget/store', '\App\Controllers\AdminFinancialWorkspaceController::storeBudget');
$routes->post('admin/financial/periods/store', '\App\Controllers\AdminFinancialWorkspaceController::storePeriod');

// Reporting Workspace Routes
$routes->get('admin/reporting', '\App\Controllers\AdminReportingWorkspaceController::index');
$routes->get('admin/reporting/preview', '\App\Controllers\AdminReportingWorkspaceController::preview');

// CMS & System Workspace Routes
$routes->get('admin/cms', '\App\Controllers\AdminCmsWorkspaceController::index');
$routes->get('admin/cms/create', '\App\Controllers\AdminCmsWorkspaceController::create');
$routes->post('admin/cms/store', '\App\Controllers\AdminCmsWorkspaceController::store');
$routes->get('admin/cms/edit/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::edit/$1/$2');
$routes->post('admin/cms/update', '\App\Controllers\AdminCmsWorkspaceController::update');
$routes->post('admin/cms/delete/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::delete/$1/$2');
$routes->get('admin/cms/delete/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::delete/$1/$2');

$routes->get('admin/settings', '\App\Controllers\AdminSystemWorkspaceController::index');
$routes->post('admin/settings/store', '\App\Controllers\AdminSystemWorkspaceController::store');

// Web Installer Routes
$routes->match(['GET', 'POST'], 'install', '\App\Controllers\InstallerController::welcome');
$routes->match(['GET', 'POST'], 'install/requirements', '\App\Controllers\InstallerController::requirements');
$routes->match(['GET', 'POST'], 'install/database', '\App\Controllers\InstallerController::database');
$routes->match(['GET', 'POST'], 'install/application', '\App\Controllers\InstallerController::application');
$routes->match(['GET', 'POST'], 'install/admin', '\App\Controllers\InstallerController::admin');
$routes->match(['GET', 'POST'], 'install/finish', '\App\Controllers\InstallerController::finish');

// Load Domain Routes
if (file_exists(APPPATH . 'Domains/Masjid/Routes/masjid.php')) {
    require APPPATH . 'Domains/Masjid/Routes/masjid.php';
}
if (file_exists(APPPATH . 'Domains/Jamaah/Routes/jamaah.php')) {
    require APPPATH . 'Domains/Jamaah/Routes/jamaah.php';
}
if (file_exists(APPPATH . 'Domains/Family/Routes/family.php')) {
    require APPPATH . 'Domains/Family/Routes/family.php';
}
if (file_exists(APPPATH . 'Domains/Financial/Routes/financial.php')) {
    require APPPATH . 'Domains/Financial/Routes/financial.php';
}
