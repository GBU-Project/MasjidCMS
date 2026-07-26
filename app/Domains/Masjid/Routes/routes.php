<?php

use App\Domains\Masjid\Controllers\MasjidController;

/**
 * Route Definitions - Domain Masjid
 * Prefix: /admin/masjid
 * Filter Pipeline: 'auth' dan 'rbac'
 */

/** @var \CodeIgniter\Router\RouteCollection $routes */

$routes->group('admin/masjid', ['filter' => ['auth', 'rbac']], function ($routes) {
    $routes->get('/', [MasjidController::class, 'index'], ['filter' => 'rbac:masjid.view']);
    $routes->get('(:segment)', [MasjidController::class, 'show'], ['filter' => 'rbac:masjid.view']);
    $routes->post('/', [MasjidController::class, 'create'], ['filter' => 'rbac:masjid.create']);
    $routes->put('(:segment)', [MasjidController::class, 'update'], ['filter' => 'rbac:masjid.update']);
    $routes->delete('(:segment)', [MasjidController::class, 'delete'], ['filter' => 'rbac:masjid.delete']);
});
