<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/**
 * TASK-019A Security Blocker Remediation (29 Juli 2026):
 * Grup rute ini sebelumnya TIDAK memiliki filter 'auth'/'rbac' sama
 * sekali -- endpoint approve/reject/post/void transaksi keuangan (yang
 * lebih otoritatif dari UI admin biasa) bisa dipanggil siapa pun tanpa
 * login. Ditambahkan filter wajib di sini.
 *
 * PERHATIAN -- tindak lanjut terpisah yang WAJIB dikerjakan (di luar
 * cakupan perubahan routing ini): FinancialApiController::approve()
 * dan reject() saat ini mengambil identitas approver dari BODY JSON
 * request ($json['approver_user_id'] ?? 'user-dkm'), bukan dari
 * SecurityContext::user() hasil sesi login. Menambahkan filter 'auth'
 * di sini MENCEGAH akses anonim, tapi TIDAK mencegah user yang sudah
 * login memalsukan approver_user_id milik user lain untuk melewati
 * ApprovalPolicy (Treasurer/Finance Manager/Chairman only). Controller
 * & Application Service/DTO terkait perlu diubah agar approver_user_id
 * diambil dari SecurityContext::user()->id, bukan dari input client.
 */
$routes->group('api/financial', ['namespace' => 'App\Controllers\Api', 'filter' => ['auth', 'rbac:financial.manage']], static function ($routes) {
    $routes->post('transactions', 'FinancialApiController::create');
    $routes->post('transactions/transfer', 'FinancialApiController::transfer');
    $routes->post('transactions/(:segment)/approve', 'FinancialApiController::approve/$1');
    $routes->post('transactions/(:segment)/reject', 'FinancialApiController::reject/$1');
    $routes->post('transactions/(:segment)/post', 'FinancialApiController::post/$1');
    $routes->post('transactions/(:segment)/void', 'FinancialApiController::void/$1');
});
