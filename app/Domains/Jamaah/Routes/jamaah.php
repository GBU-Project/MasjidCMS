<?php

use App\Domains\Jamaah\Controllers\JamaahController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

$routes->group('jamaah', function ($routes) {
    $routes->get('/', [JamaahController::class, 'index']);
    $routes->get('(:segment)', [JamaahController::class, 'show']);
    $routes->post('/', [JamaahController::class, 'create']);
    $routes->put('(:segment)', [JamaahController::class, 'update']);
    $routes->delete('(:segment)', [JamaahController::class, 'delete']);
});
