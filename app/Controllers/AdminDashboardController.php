<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminDashboardController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();

        $totalMasjids = 0;
        $totalJamaah = 0;
        $totalFamilies = 0;
        $pendingApprovals = 0;
        $todayDonation = 0;
        $totalBalance = 0;
        $recentTransactions = [];

        try {
            if ($db->tableExists('masjids')) {
                $b = $db->table('masjids');
                if ($db->fieldExists('deleted_at', 'masjids')) {
                    $b->where('deleted_at IS NULL');
                }
                $totalMasjids = $b->countAllResults();
            }
            if ($db->tableExists('jamaahs')) {
                $b = $db->table('jamaahs');
                if ($db->fieldExists('deleted_at', 'jamaahs')) {
                    $b->where('deleted_at IS NULL');
                }
                $totalJamaah = $b->countAllResults();
            }
            if ($db->tableExists('families')) {
                $b = $db->table('families');
                if ($db->fieldExists('deleted_at', 'families')) {
                    $b->where('deleted_at IS NULL');
                }
                $totalFamilies = $b->countAllResults();
            }
            if ($db->tableExists('approval_requests')) {
                $pendingApprovals = $db->table('approval_requests')->where('status', 'PENDING')->countAllResults();
            } elseif ($db->tableExists('financial_transactions')) {
                $pendingApprovals = $db->table('financial_transactions')->where('status', 'PENDING_APPROVAL')->countAllResults();
            }
            if ($db->tableExists('financial_accounts')) {
                $balanceCol = $db->fieldExists('balance', 'financial_accounts') ? 'balance' : ($db->fieldExists('cached_balance', 'financial_accounts') ? 'cached_balance' : null);
                if ($balanceCol) {
                    $query = $db->table('financial_accounts')->selectSum($balanceCol)->get();
                    $row = $query->getRow();
                    $totalBalance = (float) ($row->{$balanceCol} ?? 0);
                }
            }
            if ($db->tableExists('financial_transactions')) {
                $todayStart = date('Y-m-d 00:00:00');
                $todayEnd   = date('Y-m-d 23:59:59');
                $donQuery = $db->table('financial_transactions')
                    ->selectSum('amount')
                    ->where('transaction_type', 'INCOME')
                    ->where('transaction_date >=', $todayStart)
                    ->where('transaction_date <=', $todayEnd)
                    ->get();
                $donRow = $donQuery->getRow();
                $todayDonation = (float) ($donRow->amount ?? 0);

                $trxNoCol = $db->fieldExists('transaction_no', 'financial_transactions') ? 'transaction_no' : 'transaction_number';
                $recentTransactions = $db->table('financial_transactions')
                    ->select("{$trxNoCol} as transaction_no, transaction_type, amount, status, transaction_date, description")
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminDashboardController Exception: ' . $e->getMessage());
        }

        return view('admin/dashboard/index', [
            'totalMasjids'       => $totalMasjids,
            'totalJamaah'        => $totalJamaah,
            'totalFamilies'      => $totalFamilies,
            'pendingApprovals'   => $pendingApprovals,
            'todayDonation'      => $todayDonation,
            'totalBalance'       => $totalBalance,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
