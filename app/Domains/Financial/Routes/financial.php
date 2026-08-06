<?php

use CodeIgniter\Router\RouteCollection;
use App\Domains\Financial\Controllers\AdminFinancialWorkspaceController;

/** @var RouteCollection $routes */

/**
 * Financial Domain Admin Workspace Routes
 */
$routes->group('admin', ['filter' => ['auth', 'rbac']], static function (RouteCollection $routes) {
    $routes->get('financial', [AdminFinancialWorkspaceController::class, 'index']);
    $routes->get('financial/create', [AdminFinancialWorkspaceController::class, 'create']);
    $routes->post('financial/store', [AdminFinancialWorkspaceController::class, 'store'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/delete/(:segment)', [AdminFinancialWorkspaceController::class, 'delete'], ['filter' => 'rbac:financial.manage']);
    // RC Blocker fix: alur DRAFT -> PENDING_APPROVAL -> APPROVED -> POSTED
    // sekarang dapat dijalankan dari Admin UI, lewat Application Service
    // yang sama dipakai api/financial/*. Maker-checker (approver != pembuat)
    // ditegakkan di domain layer (FinancialTransaction::approve()).
    $routes->post('financial/submit/(:segment)', [AdminFinancialWorkspaceController::class, 'submit'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/approve/(:segment)', [AdminFinancialWorkspaceController::class, 'approveTransaction'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/post/(:segment)', [AdminFinancialWorkspaceController::class, 'postTransaction'], ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/detail/(:segment)', [AdminFinancialWorkspaceController::class, 'detail']);
    $routes->get('financial/export', [AdminFinancialWorkspaceController::class, 'export'], ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/coa/export', [AdminFinancialWorkspaceController::class, 'exportCoa'], ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/budget/export', [AdminFinancialWorkspaceController::class, 'exportBudget'], ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/periods/export', [AdminFinancialWorkspaceController::class, 'exportPeriods'], ['filter' => 'rbac:financial.manage']);
    $routes->get('financial/journal/export', [AdminFinancialWorkspaceController::class, 'exportJournal'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/import', [AdminFinancialWorkspaceController::class, 'import'], ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/coa/store', [AdminFinancialWorkspaceController::class, 'storeCoa'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/coa/delete/(:segment)', [AdminFinancialWorkspaceController::class, 'deleteCoa'], ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/budget/store', [AdminFinancialWorkspaceController::class, 'storeBudget'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/budget/delete/(:segment)', [AdminFinancialWorkspaceController::class, 'deleteBudget'], ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/periods/store', [AdminFinancialWorkspaceController::class, 'storePeriod'], ['filter' => 'rbac:financial.manage']);
    $routes->post('financial/periods/delete/(:segment)', [AdminFinancialWorkspaceController::class, 'deletePeriod'], ['filter' => 'rbac:financial.manage']);

    $routes->post('financial/journal/store', [AdminFinancialWorkspaceController::class, 'storeJournal'], ['filter' => 'rbac:financial.manage']);
});

/**
 * Financial Domain API Routes
 */
$routes->group('api/financial', ['namespace' => 'App\Controllers\Api', 'filter' => ['auth', 'rbac:financial.manage']], static function ($routes) {
    $routes->post('transactions', 'FinancialApiController::create');
    $routes->post('transactions/transfer', 'FinancialApiController::transfer');
    $routes->post('transactions/(:segment)/approve', 'FinancialApiController::approve/$1');
    $routes->post('transactions/(:segment)/reject', 'FinancialApiController::reject/$1');
    $routes->post('transactions/(:segment)/post', 'FinancialApiController::post/$1');
    $routes->post('transactions/(:segment)/void', 'FinancialApiController::void/$1');
});
