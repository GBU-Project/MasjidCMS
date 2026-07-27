<?php

use App\Domains\Family\Controllers\FamilyController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

$routes->group('family', function ($routes) {
    $routes->get('/', [FamilyController::class, 'index']);
    $routes->get('(:segment)/members', [FamilyController::class, 'members']);
    $routes->post('(:segment)/transfer-head', [FamilyController::class, 'transferHead']);
    $routes->get('(:segment)', [FamilyController::class, 'show']);
    $routes->post('/', [FamilyController::class, 'create']);
    $routes->put('(:segment)', [FamilyController::class, 'update']);
    $routes->delete('(:segment)', [FamilyController::class, 'delete']);
});
