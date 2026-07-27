<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;

class AdminReportingWorkspaceController extends BaseController
{
    public function index(): string
    {
        $tab = (string) ($this->request->getGet('tab') ?? 'catalog');

        $moduleLabels = [
            'catalog' => 'Katalog Laporan',
            'preview' => 'Preview Laporan Browser',
            'history' => 'Riwayat Generate Laporan',
        ];

        return view('admin/reporting/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Katalog Laporan',
        ]);
    }

    public function preview(): string
    {
        $type = (string) ($this->request->getGet('type') ?? 'TRIAL_BALANCE');

        $reportTitles = [
            'TRIAL_BALANCE'  => 'Laporan Neraca Saldo (Trial Balance)',
            'GENERAL_LEDGER' => 'Laporan Buku Besar (General Ledger)',
            'CASH_BOOK'      => 'Laporan Buku Kas Utama (Cash Book)',
            'FUND_BALANCE'   => 'Laporan Saldo Per Kantong Dana',
            'INCOME_EXPENSE' => 'Laporan Pendapatan & Beban Operasional',
            'AUDIT_LOG'      => 'Laporan Audit Log Activity',
        ];

        return view('admin/reporting/preview', [
            'reportType'  => $type,
            'reportTitle' => $reportTitles[$type] ?? 'Laporan Neraca Saldo',
        ]);
    }
}
