<?php

use CodeIgniter\Router\RouteCollection;

// Public Portal Routes
// Catatan TASK-019A: rute ini SENGAJA dibiarkan tanpa filter 'auth'/'rbac'
// karena memang dirancang sebagai halaman publik (profil, berita, donasi,
// transparansi, dll.) yang harus bisa diakses siapa pun tanpa login.
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
$routes->get('jadwal-sholat', '\App\Controllers\PublicPortalController::prayerTimes');
$routes->get('jadwal-shalat', '\App\Controllers\PublicPortalController::prayerSchedule');

// ---------------------------------------------------------------------
// Authentication Routes (Browser-facing, session based)
// TASK-019A Security Blocker Remediation (29 Juli 2026):
// Sebelumnya TIDAK ADA satupun rute login yang aktif di aplikasi ini
// (app/Domains/System/Routes/auth.php didefinisikan tapi tidak pernah
// di-require). Ditambahkan di sini agar mekanisme login benar-benar
// bisa diakses pengguna melalui browser.
// ---------------------------------------------------------------------
$routes->get('login', '\App\Controllers\AuthPageController::showLogin');
$routes->post('login', '\App\Controllers\AuthPageController::login');
$routes->get('logout', '\App\Controllers\AuthPageController::logout');

// Aktifkan juga JSON Auth API (auth/login, auth/logout, auth/refresh) untuk
// kebutuhan klien programatik (mobile/SPA) -- sebelumnya file ini ada tapi
// tidak pernah di-require sama sekali.
if (file_exists(APPPATH . 'Domains/System/Routes/auth.php')) {
    require APPPATH . 'Domains/System/Routes/auth.php';
}

// ---------------------------------------------------------------------
// ADMIN WORKSPACE ROUTES
// TASK-019A Security Blocker Remediation (29 Juli 2026):
// TEMUAN KRITIS sebelumnya: seluruh rute admin/* di bawah ini terdaftar
// TANPA filter 'auth'/'rbac' sama sekali, sehingga panel admin (termasuk
// modul Keuangan, User/RBAC, Settings) bisa diakses SIAPA PUN tanpa
// login. Filter AuthenticationFilter & AuthorizationFilter sudah lama
// dirancang dengan benar (lihat app/Filters/*) namun tidak pernah
// "di-wiring" ke rute-rute ini.
//
// Perbaikan: seluruh rute admin/* dibungkus $routes->group('admin', ...)
// dengan filter wajib ['auth', 'rbac']. Filter 'rbac' tanpa argumen
// hanya mensyaratkan pengguna sudah login (dan Super Admin selalu bypass
// -- lihat AuthorizationFilter::before()). Untuk aksi sensitif (mutasi
// data keuangan & manajemen user/RBAC), ditambahkan permission_code
// spesifik ('financial.manage', 'admin.manage') di level rute individual.
// ---------------------------------------------------------------------
$routes->group('admin', ['filter' => ['auth', 'rbac']], static function (RouteCollection $routes) {

    // Media Library Routes
    $routes->get('media', '\App\Controllers\AdminMediaController::index');
    $routes->get('media/api', '\App\Controllers\AdminMediaController::apiList');
    $routes->post('media/upload', '\App\Controllers\AdminMediaController::upload');
    $routes->post('media/delete/(:segment)', '\App\Controllers\AdminMediaController::delete/$1');
    $routes->post('media/bulk-delete', '\App\Controllers\AdminMediaController::bulkDelete');
    $routes->post('media/rename', '\App\Controllers\AdminMediaController::rename');

    // My Profile / Change Password Routes (TASK-022 finding B)
    $routes->get('profile', '\App\Controllers\AdminProfileController::index');
    $routes->get('profile/password', '\App\Controllers\AdminProfileController::password');
    $routes->post('profile/password', '\App\Controllers\AdminProfileController::updatePassword');

    // Admin Workspace Routes
    $routes->get('dashboard', '\App\Controllers\AdminDashboardController::index');

    // Master Data Workspace Routes (User/Role/Permission management -> permission sensitif)
    $routes->get('master', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('master/create', '\App\Controllers\AdminMasterDataController::create');
    $routes->post('master/store', '\App\Controllers\AdminMasterDataController::store', ['filter' => 'rbac:admin.manage']);
    $routes->get('master/edit/(:segment)/(:segment)', '\App\Controllers\AdminMasterDataController::edit/$1/$2');
    $routes->post('master/update', '\App\Controllers\AdminMasterDataController::update', ['filter' => 'rbac:admin.manage']);
    $routes->get('master/delete/(:segment)/(:segment)', '\App\Controllers\AdminMasterDataController::delete/$1/$2', ['filter' => 'rbac:admin.manage']);
    $routes->get('masjid', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('bidang', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('pengurus', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('jamaah', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('family', '\App\Controllers\AdminMasterDataController::index');
    $routes->get('users', '\App\Controllers\AdminMasterDataController::index', ['filter' => 'rbac:admin.manage']);
    $routes->get('rbac', '\App\Controllers\AdminMasterDataController::index', ['filter' => 'rbac:admin.manage']);

    // Financial Workspace Routes (mutasi dana masjid -> permission sensitif)
    $routes->get('financial', '\App\Controllers\AdminFinancialWorkspaceController::index');
    $routes->get('financial/create', '\App\Controllers\AdminFinancialWorkspaceController::create');
    $routes->post('financial/store', '\App\Controllers\AdminFinancialWorkspaceController::store', ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::delete/$1', ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/detail/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::detail/$1');
    $routes->get('financial/export', '\App\Controllers\AdminFinancialWorkspaceController::export');
    $routes->post('financial/import', '\App\Controllers\AdminFinancialWorkspaceController::import', ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/coa/store', '\App\Controllers\AdminFinancialWorkspaceController::storeCoa', ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/coa/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deleteCoa/$1', ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/budget/store', '\App\Controllers\AdminFinancialWorkspaceController::storeBudget', ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/budget/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deleteBudget/$1', ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/periods/store', '\App\Controllers\AdminFinancialWorkspaceController::storePeriod', ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/periods/delete/(:segment)', '\App\Controllers\AdminFinancialWorkspaceController::deletePeriod/$1', ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/journal/store', '\App\Controllers\AdminFinancialWorkspaceController::storeJournal', ['filter' => 'rbac:financial.manage']);

    // Reporting Workspace Routes (read-only, cukup 'auth')
    $routes->get('reporting', '\App\Controllers\AdminReportingWorkspaceController::index');
    $routes->get('reporting/preview', '\App\Controllers\AdminReportingWorkspaceController::preview');

    // CMS & System Workspace Routes
    $routes->get('cms', '\App\Controllers\AdminCmsWorkspaceController::index');
    $routes->get('cms/create', '\App\Controllers\AdminCmsWorkspaceController::create');
    $routes->post('cms/store', '\App\Controllers\AdminCmsWorkspaceController::store', ['filter' => 'rbac:admin.manage']);
    $routes->get('cms/edit/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::edit/$1/$2');
    $routes->post('cms/update', '\App\Controllers\AdminCmsWorkspaceController::update', ['filter' => 'rbac:admin.manage']);
    $routes->post('cms/delete/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::delete/$1/$2', ['filter' => 'rbac:admin.manage']);
    $routes->get('cms/delete/(:segment)/(:segment)', '\App\Controllers\AdminCmsWorkspaceController::delete/$1/$2', ['filter' => 'rbac:admin.manage']);
    $routes->get('program', '\App\Controllers\AdminCmsWorkspaceController::index');
    $routes->get('layanan-cms', '\App\Controllers\AdminCmsWorkspaceController::index');

    // Website & Homepage Manager Routes
    $routes->get('homepage-manager', '\App\Controllers\AdminHomepageManagerController::index');
    $routes->post('homepage-manager/save-order', '\App\Controllers\AdminHomepageManagerController::saveOrder', ['filter' => 'rbac:admin.manage']);
    $routes->post('homepage-manager/save-settings', '\App\Controllers\AdminHomepageManagerController::saveSettings', ['filter' => 'rbac:admin.manage']);
    $routes->post('homepage-manager/bulk-action', '\App\Controllers\AdminHomepageManagerController::bulkAction', ['filter' => 'rbac:admin.manage']);
    $routes->post('homepage-manager/reset-default', '\App\Controllers\AdminHomepageManagerController::resetDefault', ['filter' => 'rbac:admin.manage']);
    $routes->post('homepage-manager/clear-cache', '\App\Controllers\AdminHomepageManagerController::clearCache', ['filter' => 'rbac:admin.manage']);
    $routes->get('theme', '\App\Controllers\AdminSystemWorkspaceController::index');
    $routes->post('theme/store', '\App\Controllers\AdminSystemWorkspaceController::storeTheme', ['filter' => 'rbac:admin.manage']);

    $routes->get('settings', '\App\Controllers\AdminSystemWorkspaceController::index');
    $routes->post('settings/store', '\App\Controllers\AdminSystemWorkspaceController::store', ['filter' => 'rbac:admin.manage']);
    $routes->get('settings/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteSetting/$1', ['filter' => 'rbac:admin.manage']);

    $routes->get('menu', '\App\Controllers\AdminSystemWorkspaceController::index');
    $routes->post('menu/store', '\App\Controllers\AdminSystemWorkspaceController::storeMenu', ['filter' => 'rbac:admin.manage']);
    $routes->get('menu/edit/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::editMenu/$1');
    $routes->post('menu/update', '\App\Controllers\AdminSystemWorkspaceController::updateMenu', ['filter' => 'rbac:admin.manage']);
    $routes->get('menu/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteMenu/$1', ['filter' => 'rbac:admin.manage']);

    // NOTE (pra-eksisting, di luar cakupan TASK-019A): rute 'admin/media' &
    // 'admin/media/*' berikut ini duplikat/di-shadow oleh AdminMediaController
    // di atas (didaftarkan lebih dulu sehingga versi AdminSystemWorkspaceController
    // ini tidak pernah tereksekusi). Dipertahankan apa adanya agar scope
    // perubahan tetap fokus ke perbaikan kontrol akses; direkomendasikan
    // dibersihkan terpisah.
    $routes->get('media', '\App\Controllers\AdminSystemWorkspaceController::index');
    $routes->post('media/store', '\App\Controllers\AdminSystemWorkspaceController::storeMedia', ['filter' => 'rbac:admin.manage']);
    $routes->get('media/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteMedia/$1', ['filter' => 'rbac:admin.manage']);

    $routes->get('notification', '\App\Controllers\AdminSystemWorkspaceController::index');
    $routes->post('notification/store', '\App\Controllers\AdminSystemWorkspaceController::storeNotification', ['filter' => 'rbac:admin.manage']);
    $routes->get('notification/delete/(:segment)', '\App\Controllers\AdminSystemWorkspaceController::deleteNotification/$1', ['filter' => 'rbac:admin.manage']);

    // Prayer Time Routes
    $routes->get('prayer-time', '\App\Controllers\AdminPrayerTimeController::index');
    $routes->post('prayer-time/update', '\App\Controllers\AdminPrayerTimeController::update', ['filter' => 'rbac:admin.manage']);
    $routes->post('prayer-time/reset-default', '\App\Controllers\AdminPrayerTimeController::resetDefault', ['filter' => 'rbac:admin.manage']);
    $routes->get('prayer-time/api', '\App\Controllers\AdminPrayerTimeController::apiGetTimes');
});

// Web Installer Routes
// Catatan: rute ini tetap tanpa filter 'auth' by design (dipakai sebelum
// akun admin pertama ada), sudah dikecualikan pula dari filter CSRF global
// di app/Config/Filters.php ('except' => ['install/*', 'install']).
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
