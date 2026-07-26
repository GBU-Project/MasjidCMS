<?php

use App\Domains\System\Controllers\AuthenticationController;

/**
 * Route Placeholder - Domain System (Authentication)
 * Note: Rute belum diaktifkan secara global di App/Config/Routes.php
 */

/** @var \CodeIgniter\Router\RouteCollection $routes */

$routes->group('auth', function ($routes) {
    $routes->get('login', [AuthenticationController::class, 'login']);
    $routes->post('login', [AuthenticationController::class, 'login']);
    $routes->post('logout', [AuthenticationController::class, 'logout']);
    $routes->post('refresh', [AuthenticationController::class, 'refresh']);
});
