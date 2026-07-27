<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

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
        $db = Database::connect();
        $type = (string) ($this->request->getGet('type') ?? 'TRIAL_BALANCE');

        $reportTitles = [
            'TRIAL_BALANCE'  => 'Laporan Neraca Saldo (Trial Balance)',
            'GENERAL_LEDGER' => 'Laporan Buku Besar (General Ledger)',
            'CASH_BOOK'      => 'Laporan Buku Kas Utama (Cash Book)',
            'FUND_BALANCE'   => 'Laporan Saldo Per Kantong Dana',
            'INCOME_EXPENSE' => 'Laporan Pendapatan & Beban Operasional',
            'AUDIT_LOG'      => 'Laporan Audit Log Activity',
        ];

        $totalRecords = 0;
        $totalIncome = 0.0;
        $totalExpense = 0.0;
        $netBalance = 0.0;

        if ($db->tableExists('financial_transactions')) {
            $totalRecords = $db->table('financial_transactions')->countAllResults();

            $incQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'INCOME')->get();
            $totalIncome = (float) ($incQuery->getRow()->amount ?? 0);

            $expQuery = $db->table('financial_transactions')->selectSum('amount')->where('transaction_type', 'EXPENSE')->get();
            $totalExpense = (float) ($expQuery->getRow()->amount ?? 0);

            $netBalance = $totalIncome - $totalExpense;
        }

        return view('admin/reporting/preview', [
            'reportType'   => $type,
            'reportTitle'  => $reportTitles[$type] ?? 'Laporan Neraca Saldo',
            'totalRecords' => $totalRecords,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance'   => $netBalance,
        ]);
    }
}
