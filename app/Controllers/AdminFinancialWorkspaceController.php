<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminFinancialWorkspaceController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getGet('tab') ?? 'transactions');

        $headers = [];
        $rows = [];

        if ($tab === 'transactions' && $db->tableExists('financial_transactions')) {
            $headers = ['No. Transaksi', 'Tanggal', 'Jenis', 'Nominal (Rp)', 'Status'];
            $data = $db->table('financial_transactions')->orderBy('created_at', 'DESC')->get()->getResultArray();
            foreach ($data as $t) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono"><a href="/admin/financial/detail/' . esc($t['transaction_number']) . '">' . esc($t['transaction_number']) . '</a></span>',
                        esc(substr($t['transaction_date'], 0, 10)),
                        '<span class="badge ' . ($t['transaction_type'] === 'INCOME' ? 'badge-green' : 'badge-red') . '">' . esc($t['transaction_type']) . '</span>',
                        '<span class="stat-mono">' . number_format((float)$t['amount'], 0, ',', '.') . '</span>',
                        '<span class="badge ' . ($t['status'] === 'POSTED' ? 'badge-green' : 'badge-amber') . '">' . esc($t['status']) . '</span>',
                    ]
                ];
            }
        } elseif ($tab === 'approvals' && $db->tableExists('approval_requests')) {
            $headers = ['Request ID', 'Transaction ID', 'Requester', 'Status', 'Tanggal Request'];
            $data = $db->table('approval_requests')->get()->getResultArray();
            foreach ($data as $a) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">REQ-' . esc($a['id']) . '</span>',
                        '<span class="stat-mono">TRX-' . esc($a['transaction_id']) . '</span>',
                        esc($a['requester_id']),
                        '<span class="badge badge-amber">' . esc($a['status']) . '</span>',
                        esc(substr($a['created_at'], 0, 16)),
                    ]
                ];
            }
        } elseif ($tab === 'transfer' && $db->tableExists('funds')) {
            $headers = ['Kode Fund', 'Nama Kantong Dana', 'Tipe Syariah', 'Deskripsi'];
            $data = $db->table('funds')->get()->getResultArray();
            foreach ($data as $f) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($f['fund_code']) . '</span>',
                        '<strong>' . esc($f['name']) . '</strong>',
                        '<span class="badge badge-green">' . esc($f['fund_type']) . '</span>',
                        esc($f['description'] ?? '-'),
                    ]
                ];
            }
        } elseif ($tab === 'journal' && $db->tableExists('journal_entries')) {
            $headers = ['No. Jurnal', 'Ref Transaksi', 'Tanggal', 'Keterangan', 'Status Balance'];
            $data = $db->table('journal_entries')->get()->getResultArray();
            foreach ($data as $j) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($j['journal_number']) . '</span>',
                        '<span class="stat-mono">TRX-' . esc($j['transaction_id']) . '</span>',
                        esc(substr($j['journal_date'], 0, 10)),
                        esc($j['description']),
                        '<span class="badge badge-green">' . ($j['is_balanced'] ? 'BALANCED' : 'UNBALANCED') . '</span>',
                    ]
                ];
            }
        }

        $moduleLabels = [
            'transactions' => 'Daftar Transaksi',
            'approvals'    => 'Queue Persetujuan DKM',
            'transfer'     => 'Transfer Kantong Dana',
            'journal'      => 'Buku Jurnal Umum',
        ];

        return view('admin/financial/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Daftar Transaksi',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }

    public function create(): string
    {
        return view('admin/financial/create');
    }

    public function detail(string $id): string
    {
        return view('admin/financial/detail', [
            'transactionId' => $id
        ]);
    }
}
