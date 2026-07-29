<?php

use App\Domains\Jamaah\Controllers\JamaahController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

/**
 * TASK-019A Security Blocker Remediation (29 Juli 2026):
 * Data jamaah adalah data pribadi (PII). Grup rute ini sebelumnya bisa
 * diakses publik tanpa login sama sekali (termasuk create/update/delete).
 * Ditambahkan filter 'auth' + 'rbac' memakai permission_code yang sudah
 * ada di RbacSeeder ('jamaah.read', 'jamaah.create', dst).
 */
$routes->group('jamaah', ['filter' => ['auth', 'rbac']], function ($routes) {
    $routes->get('/', [JamaahController::class, 'index'], ['filter' => 'rbac:jamaah.read']);
    $routes->get('(:segment)', [JamaahController::class, 'show'], ['filter' => 'rbac:jamaah.read']);
    $routes->post('/', [JamaahController::class, 'create'], ['filter' => 'rbac:jamaah.create']);
    $routes->put('(:segment)', [JamaahController::class, 'update'], ['filter' => 'rbac:jamaah.update']);
    $routes->delete('(:segment)', [JamaahController::class, 'delete'], ['filter' => 'rbac:jamaah.delete']);
});
