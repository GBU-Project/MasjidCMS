<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;

class AdminFinancialWorkspaceController extends BaseController
{
    public function index(): string
    {
        $tab = (string) ($this->request->getGet('tab') ?? 'transactions');

        $headers = ['No. Transaksi', 'Tanggal', 'Jenis', 'Kantong Dana', 'Deskripsi', 'Nominal (Rp)', 'Status'];
        $rows = [
            [
                'columns' => [
                    '<span class="stat-mono"><a href="/admin/financial/detail/TRX-202607-00088">TRX-202607-00088</a></span>',
                    '27 Jul 2026',
                    '<span class="badge badge-red">EXPENSE</span>',
                    'Kas Umum',
                    'Beban Kebersihan Halaman',
                    '<span class="stat-mono">250.000</span>',
                    '<span class="badge badge-amber">PENDING</span>'
                ]
            ],
            [
                'columns' => [
                    '<span class="stat-mono"><a href="/admin/financial/detail/TRX-202607-00080">TRX-202607-00080</a></span>',
                    '27 Jul 2026',
                    '<span class="badge badge-green">INCOME</span>',
                    'Kas Umum',
                    'Infaq Kotak Jumat',
                    '<span class="stat-mono">500.000</span>',
                    '<span class="badge badge-green">POSTED</span>'
                ]
            ],
        ];

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
