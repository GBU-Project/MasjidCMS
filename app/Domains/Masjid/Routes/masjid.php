<?php

use App\Domains\Masjid\Controllers\MasjidController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

$routes->group('masjid', function ($routes) {
    $routes->get('/', [MasjidController::class, 'index']);
    $routes->get('(:segment)', [MasjidController::class, 'show']);
    $routes->post('/', [MasjidController::class, 'create']);
    $routes->put('(:segment)', [MasjidController::class, 'update']);
    $routes->delete('(:segment)', [MasjidController::class, 'delete']);
});
