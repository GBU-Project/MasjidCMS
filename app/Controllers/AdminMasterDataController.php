<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;

class AdminMasterDataController extends BaseController
{
    public function index(): string
    {
        $tab = (string) ($this->request->getGet('tab') ?? 'jamaah');

        $headers = ['Kode / ID', 'Nama Lengkap', 'Kategori', 'Tanggal Daftar', 'Status'];
        $rows = [
            [
                'columns' => [
                    '<span class="stat-mono">JAM-2026-0001</span>',
                    '<strong>Bpk. Ahmad Subagyo</strong>',
                    'Jamaah Tetap',
                    '15 Jan 2026',
                    '<span class="badge badge-green">AKTIF</span>'
                ]
            ],
            [
                'columns' => [
                    '<span class="stat-mono">JAM-2026-0002</span>',
                    '<strong>Ibu Siti Fatimah</strong>',
                    'Jamaah Tetap',
                    '18 Jan 2026',
                    '<span class="badge badge-green">AKTIF</span>'
                ]
            ],
            [
                'columns' => [
                    '<span class="stat-mono">JAM-2026-0003</span>',
                    '<strong>Bpk. Ridwan Kamil</strong>',
                    'Donatur',
                    '01 Feb 2026',
                    '<span class="badge badge-green">AKTIF</span>'
                ]
            ],
        ];

        $moduleLabels = [
            'masjid'     => 'Profil Masjid',
            'jamaah'     => 'Data Jamaah',
            'family'     => 'Data Keluarga',
            'user'       => 'User Accounts',
            'role'       => 'Role Access',
            'permission' => 'Permissions',
        ];

        return view('admin/master/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Data Jamaah',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }
}
