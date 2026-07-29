<?php

use CodeIgniter\Router\RouteCollection;

// Public Portal Routes
$routes->get('/', '\App\Controllers\PublicPortalController::index');
$routes->get('profil', '\App\Controllers\PublicPortalController::profile');
$routes->get('struktur-organisasi', '\App\Controllers\PublicPortalController::orgStructure');
$routes->get('berita', '\App\Controllers\PublicPortalController::news');
$routes->get('program', '\App\Controllers\PublicPortalController::programs');
$routes->get('layanan', '\App\Controllers\PublicPortalController::services');
$routes->get('donasi', '\App\Controllers\PublicPortalController::donation');
$routes->get('kontak', '\App\Controllers\PublicPortalController::contact');
$routes->get('galeri', '\App\Controllers\PublicPortalController::gallery');
$routes->get('transparansi', '\App\Controllers\PublicPortalController::transparency');

// Media Library Routes
$routes->get('admin/media', '\App\Controllers\AdminMediaController::index');
$routes->get('admin/media/api', '\App\Controllers\AdminMediaController::apiList');
$routes->post('admin/media/upload', '\App\Controllers\AdminMediaController::upload');
$routes->post('admin/media/delete/(:segment)', '\App\Controllers\AdminMediaController::delete/$1');
$routes->post('admin/media/bulk-delete', '\App\Controllers\AdminMediaController::bulkDelete');
$routes->post('admin/media/rename', '\App\Controllers\AdminMediaController::rename');

// Admin Workspace Routes
$routes->get('admin/dashboard', '\App\Controllers\AdminDashboardController::index');

// Master Data Workspace Routes
$routes->get('admin/master', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/master/create', '\App\Controllers\AdminMasterDataController::create');
$routes->post('admin/master/store', '\App\Controllers\AdminMasterDataController::store');
$routes->get('admin/master/edit/(:segment)/(:segment)', '\App\Controllers\AdminMasterDataController::edit/$1/$2');
$routes->post('admin/master/update', '\App\Controllers\AdminMasterDataController::update');
$routes->get('admin/master/delete/(:segment)/(:segment)', '\App\Controllers\AdminMasterDataController::delete/$1/$2');
$routes->get('admin/masjid', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/bidang', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/pengurus', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/jamaah', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/family', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/users', '\App\Controllers\AdminMasterDataController::index');
$routes->get('admin/rbac', '\App\Controllers\AdminMasterDataController::index');

// Financial Workspace Routes
$routes->get('admin/financial', '\App\Controllers\AdminFinancialWorkspaceController::index');
$routes->get('admin/financial/create', '\App\Controllers\AdminFinancialWorkspaceController::create');
$routes->post('admin/financial/store', '\App\Controllers\AdminFinancialWorkspaceController::store');
$routes->get('admin/financial/edit/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::edit/$1');
$routes->post('admin/financial/update', '\App\Controllers\AdminFinancialWorkspaceController::update');
$routes->get('admin/financial/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::delete/$1');
$routes->get('admin/financial/detail/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::detail/$1');

$routes->post('admin/financial/coa/store', '\App\Controllers\AdminFinancialWorkspaceController::storeCoa');
$routes->get('admin/financial/coa/edit/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::editCoa/$1');
$routes->post('admin/financial/coa/update', '\App\Controllers\AdminFinancialWorkspaceController::updateCoa');
$routes->get('admin/financial/coa/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deleteCoa/$1');

$routes->post('admin/financial/budget/store', '\App\Controllers\AdminFinancialWorkspaceController::storeBudget');
$routes->get('admin/financial/budget/edit/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::editBudget/$1');
$routes->post('admin/financial/budget/update', '\App\Controllers\AdminFinancialWorkspaceController::updateBudget');
$routes->get('admin/financial/budget/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deleteBudget/$1');

$routes->post('admin/financial/periods/store', '\App\Controllers\AdminFinancialWorkspaceController::storePeriod');
$routes->get('admin/financial/periods/edit/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::editPeriod/$1');
$routes->post('admin/financial/periods/update', '\App\Controllers\AdminFinancialWorkspaceController::updatePeriod');
$routes->get('admin/financial/periods/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deletePeriod/$1');

$routes->post('admin/financial/journal/store', '\App\Controllers\AdminFinancialWorkspaceController::storeJournal');

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
$routes->get('admin/program', '\App\Controllers\AdminCmsWorkspaceController::index');
$routes->get('admin/layanan-cms', '\App\Controllers\AdminCmsWorkspaceController::index');

// Website & Homepage Manager Routes
$routes->get('admin/homepage-manager', '\App\Controllers\AdminHomepageManagerController::index');
$routes->post('admin/homepage-manager/save-order', '\App\Controllers\AdminHomepageManagerController::saveOrder');
$routes->post('admin/homepage-manager/save-settings', '\App\Controllers\AdminHomepageManagerController::saveSettings');
$routes->post('admin/homepage-manager/bulk-action', '\App\Controllers\AdminHomepageManagerController::bulkAction');
$routes->post('admin/homepage-manager/reset-default', '\App\Controllers\AdminHomepageManagerController::resetDefault');
$routes->post('admin/homepage-manager/clear-cache', '\App\Controllers\AdminHomepageManagerController::clearCache');
$routes->get('admin/theme', '\App\Controllers\AdminSystemWorkspaceController::index');

$routes->get('admin/settings', '\App\Controllers\AdminSystemWorkspaceController::index');
$routes->post('admin/settings/store', '\App\Controllers\AdminSystemWorkspaceController::store');
$routes->get('admin/settings/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteSetting/$1');

$routes->get('admin/menu', '\App\Controllers\AdminSystemWorkspaceController::index');
$routes->post('admin/menu/store', '\App\Controllers\AdminSystemWorkspaceController::storeMenu');
$routes->get('admin/menu/edit/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::editMenu/$1');
$routes->post('admin/menu/update', '\App\Controllers\AdminSystemWorkspaceController::updateMenu');
$routes->get('admin/menu/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteMenu/$1');

$routes->get('admin/media', '\App\Controllers\AdminSystemWorkspaceController::index');
$routes->post('admin/media/store', '\App\Controllers\AdminSystemWorkspaceController::storeMedia');
$routes->get('admin/media/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteMedia/$1');

$routes->get('admin/notification', '\App\Controllers\AdminSystemWorkspaceController::index');
$routes->post('admin/notification/store', '\App\Controllers\AdminSystemWorkspaceController::storeNotification');
$routes->get('admin/notification/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteNotification/$1');

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
