<?php

use App\Domains\Masjid\Controllers\MasjidController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

/**
 * TASK-019A Security Blocker Remediation (29 Juli 2026):
 * Grup rute ini sebelumnya TIDAK memiliki filter 'auth'/'rbac' sama sekali,
 * sehingga endpoint create/update/delete data masjid bisa diakses publik
 * tanpa login. Ditambahkan filter pipeline sesuai desain awal yang sudah
 * benar di app/Domains/Masjid/Routes/routes.php (namun file itu sendiri
 * tidak pernah di-load/dead code -- lihat catatan di routes.php).
 *
 * Catatan: halaman publik profil masjid (mis. /profil) dilayani oleh
 * PublicPortalController secara terpisah dan TIDAK terpengaruh oleh
 * perubahan ini -- perubahan ini hanya menutup akses ke domain internal
 * MasjidController (CRUD data mentah).
 */
$routes->group('masjid', ['filter' => ['auth', 'rbac']], function ($routes) {
    $routes->get('/', [MasjidController::class, 'index'], ['filter' => 'rbac:masjid.read']);
    $routes->get('(:segment)', [MasjidController::class, 'show'], ['filter' => 'rbac:masjid.read']);
    $routes->post('/', [MasjidController::class, 'create'], ['filter' => 'rbac:masjid.create']);
    $routes->put('(:segment)', [MasjidController::class, 'update'], ['filter' => 'rbac:masjid.update']);
    $routes->delete('(:segment)', [MasjidController::class, 'delete'], ['filter' => 'rbac:masjid.delete']);
});
