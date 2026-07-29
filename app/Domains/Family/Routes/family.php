<?php

use App\Domains\Family\Controllers\FamilyController;

/** @var \CodeIgniter\Router\RouteCollection $routes */

/**
 * TASK-019A Security Blocker Remediation (29 Juli 2026):
 * Data keluarga (termasuk operasi sensitif seperti transfer kepala
 * keluarga/pemindahan anggota) sebelumnya bisa diakses publik tanpa
 * login. Ditambahkan filter 'auth' + 'rbac' memakai permission_code
 * yang sudah ada di RbacSeeder ('family.read', 'family.create', dst).
 */
$routes->group('family', ['filter' => ['auth', 'rbac']], function ($routes) {
    $routes->get('/', [FamilyController::class, 'index'], ['filter' => 'rbac:family.read']);
    $routes->get('(:segment)/members', [FamilyController::class, 'members'], ['filter' => 'rbac:family.read']);
    $routes->post('(:segment)/members', [FamilyController::class, 'addMember'], ['filter' => 'rbac:family.update']);
    $routes->delete('(:segment)/members/(:segment)', [FamilyController::class, 'removeMember'], ['filter' => 'rbac:family.update']);
    $routes->put('(:segment)/members/(:segment)/relation', [FamilyController::class, 'changeRelation'], ['filter' => 'rbac:family.update']);
    $routes->post('(:segment)/move-member', [FamilyController::class, 'moveMember'], ['filter' => 'rbac:family.update']);
    $routes->post('(:segment)/transfer-head', [FamilyController::class, 'transferHead'], ['filter' => 'rbac:family.update']);
    $routes->get('(:segment)', [FamilyController::class, 'show'], ['filter' => 'rbac:family.read']);
    $routes->post('/', [FamilyController::class, 'create'], ['filter' => 'rbac:family.create']);
    $routes->put('(:segment)', [FamilyController::class, 'update'], ['filter' => 'rbac:family.update']);
    $routes->delete('(:segment)', [FamilyController::class, 'delete'], ['filter' => 'rbac:family.delete']);
});
