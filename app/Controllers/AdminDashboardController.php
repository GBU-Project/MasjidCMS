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
        $totalBalance = 0;
        $recentTransactions = [];

        if ($db->tableExists('masjids')) {
            $totalMasjids = $db->table('masjids')->countAllResults();
        }
        if ($db->tableExists('jamaah')) {
            $totalJamaah = $db->table('jamaah')->countAllResults();
        }
        if ($db->tableExists('families')) {
            $totalFamilies = $db->table('families')->countAllResults();
        }
        if ($db->tableExists('approval_requests')) {
            $pendingApprovals = $db->table('approval_requests')->where('status', 'PENDING')->countAllResults();
        } elseif ($db->tableExists('financial_transactions')) {
            $pendingApprovals = $db->table('financial_transactions')->where('status', 'PENDING_APPROVAL')->countAllResults();
        }
        if ($db->tableExists('financial_accounts')) {
            $query = $db->table('financial_accounts')->selectSum('cached_balance')->get();
            $row = $query->getRow();
            $totalBalance = (float) ($row->cached_balance ?? 0);
        }
        if ($db->tableExists('financial_transactions')) {
            $recentTransactions = $db->table('financial_transactions')
                ->select('transaction_number, transaction_type, amount, status, created_at')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        return view('admin/dashboard/index', [
            'totalMasjids'       => $totalMasjids,
            'totalJamaah'        => $totalJamaah,
            'totalFamilies'      => $totalFamilies,
            'pendingApprovals'   => $pendingApprovals,
            'totalBalance'       => $totalBalance,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
