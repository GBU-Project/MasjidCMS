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

        try {
            if ($tab === 'transactions' && $db->tableExists('financial_transactions')) {
                $headers = ['No. Transaksi', 'Tanggal', 'Jenis', 'Nominal (Rp)', 'Status'];
                $data = $db->table('financial_transactions')->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $t) {
                    $trxNo = $t['transaction_no'] ?? $t['transaction_number'] ?? ('TRX-' . $t['id']);
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono"><a href="/admin/financial/detail/' . esc($trxNo) . '">' . esc($trxNo) . '</a></span>',
                            esc(substr($t['transaction_date'], 0, 10)),
                            '<span class="badge ' . ($t['transaction_type'] === 'INCOME' ? 'badge-green' : 'badge-red') . '">' . esc($t['transaction_type']) . '</span>',
                            '<span class="stat-mono">' . number_format((float)$t['amount'], 0, ',', '.') . '</span>',
                            '<span class="badge ' . ($t['status'] === 'POSTED' ? 'badge-green' : 'badge-amber') . '">' . esc($t['status']) . '</span>',
                        ]
                    ];
                }
            } elseif ($tab === 'coa' && $db->tableExists('coa_accounts')) {
                $headers = ['Kode COA', 'Nama Akun', 'Tipe Akun', 'Status'];
                $data = $db->table('coa_accounts')->get()->getResultArray();
                foreach ($data as $c) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($c['account_code']) . '</span>',
                            '<strong>' . esc($c['name']) . '</strong>',
                            '<span class="badge badge-green">' . esc($c['account_type']) . '</span>',
                            '<span class="badge ' . ($c['is_active'] ? 'badge-green' : 'badge-amber') . '">' . ($c['is_active'] ? 'ACTIVE' : 'INACTIVE') . '</span>',
                        ]
                    ];
                }
            } elseif ($tab === 'budget' && $db->tableExists('budget')) {
                $headers = ['Budget ID', 'Period ID', 'Allocated Amount', 'Used Amount'];
                $data = $db->table('budget')->get()->getResultArray();
                foreach ($data as $b) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">BGT-' . esc($b['id']) . '</span>',
                            '<span class="stat-mono">PER-' . esc($b['period_id']) . '</span>',
                            '<span class="stat-mono">Rp ' . number_format((float)$b['allocated_amount'], 0, ',', '.') . '</span>',
                            '<span class="stat-mono">Rp ' . number_format((float)$b['used_amount'], 0, ',', '.') . '</span>',
                        ]
                    ];
                }
            } elseif ($tab === 'periods' && $db->tableExists('financial_periods')) {
                $headers = ['Kode Periode', 'Nama Periode', 'Tanggal Mulai', 'Tanggal Selesai', 'Status Closing'];
                $data = $db->table('financial_periods')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($p['period_code']) . '</span>',
                            '<strong>' . esc($p['name']) . '</strong>',
                            esc($p['start_date']),
                            esc($p['end_date']),
                            '<span class="badge ' . ($p['is_closed'] ? 'badge-amber' : 'badge-green') . '">' . ($p['is_closed'] ? 'CLOSED' : 'OPEN') . '</span>',
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
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Exception: ' . $e->getMessage());
        }

        $moduleLabels = [
            'transactions' => 'Daftar Transaksi',
            'coa'          => 'Chart of Accounts (COA)',
            'budget'       => 'Anggaran & RAB',
            'periods'      => 'Periode Akuntansi & Tutup Buku',
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

    public function store()
    {
        $db = Database::connect();
        try {
            $type = (string) ($this->request->getPost('transaction_type') ?: 'EXPENSE');
            $amount = (float) $this->request->getPost('amount');
            $desc = (string) $this->request->getPost('description');
            $date = (string) ($this->request->getPost('transaction_date') ?: date('Y-m-d H:i:s'));

            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $trxNo = 'TRX-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 9999), 5, '0', STR_PAD_LEFT);

            // Get first fund & account if exists
            $fundId = 1;
            $accountId = 1;
            $finAccId = 1;

            if ($db->tableExists('funds')) {
                $fRow = $db->table('funds')->get()->getRowArray();
                if ($fRow) {
                    $fundId = $fRow['id'];
                }
            }
            if ($db->tableExists('coa_accounts')) {
                $cRow = $db->table('coa_accounts')->get()->getRowArray();
                if ($cRow) {
                    $accountId = $cRow['id'];
                }
            }
            if ($db->tableExists('financial_accounts')) {
                $faRow = $db->table('financial_accounts')->get()->getRowArray();
                if ($faRow) {
                    $finAccId = $faRow['id'];
                }
            }

            $db->table('financial_transactions')->insert([
                'uuid'                 => $uuid,
                'masjid_id'            => '1',
                'fund_id'              => $fundId,
                'account_id'           => $accountId,
                'financial_account_id' => $finAccId,
                'transaction_no'       => $trxNo,
                'transaction_type'     => $type,
                'amount'               => $amount,
                'payment_method'       => 'CASH',
                'status'               => 'POSTED',
                'transaction_date'     => $date,
                'description'          => $desc,
                'created_at'           => date('Y-m-d H:i:s'),
            ]);

            // Update balance on financial_accounts
            if ($db->tableExists('financial_accounts')) {
                if ($type === 'INCOME') {
                    $db->query("UPDATE financial_accounts SET balance = balance + {$amount} WHERE id = {$finAccId}");
                } else {
                    $db->query("UPDATE financial_accounts SET balance = balance - {$amount} WHERE id = {$finAccId}");
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Store Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial'));
    }

    public function detail(string $id): string
    {
        $db = Database::connect();
        $transaction = null;
        try {
            if ($db->tableExists('financial_transactions')) {
                $builder = $db->table('financial_transactions');
                if ($db->fieldExists('transaction_no', 'financial_transactions')) {
                    $builder->groupStart()
                        ->where('transaction_no', $id)
                        ->orWhere('id', $id)
                        ->groupEnd();
                } else {
                    $builder->where('id', $id);
                }
                $transaction = $builder->get()->getRowArray();
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Detail Exception: ' . $e->getMessage());
        }

        return view('admin/financial/detail', [
            'transactionId' => $id,
            'transaction'   => $transaction,
        ]);
    }
}
