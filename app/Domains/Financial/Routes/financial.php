<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->group('api/financial', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('transactions', 'FinancialApiController::create');
    $routes->post('transactions/transfer', 'FinancialApiController::transfer');
    $routes->post('transactions/(:segment)/approve', 'FinancialApiController::approve/$1');
    $routes->post('transactions/(:segment)/reject', 'FinancialApiController::reject/$1');
    $routes->post('transactions/(:segment)/post', 'FinancialApiController::post/$1');
    $routes->post('transactions/(:segment)/void', 'FinancialApiController::void/$1');
});
