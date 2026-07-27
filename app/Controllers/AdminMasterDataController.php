<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminMasterDataController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getGet('tab') ?? 'profil');

        $headers = [];
        $rows = [];

        if ($tab === 'profil' && $db->tableExists('masjids')) {
            $headers = ['Nama Masjid', 'Kode Scope', 'Kota / Lokasi', 'Status Legalitas', 'Telepon'];
            $data = $db->table('masjids')->get()->getResultArray();
            foreach ($data as $m) {
                $rows[] = [
                    'columns' => [
                        '<strong>' . esc($m['name']) . '</strong>',
                        '<span class="stat-mono">' . esc($m['code']) . '</span>',
                        esc($m['city']),
                        '<span class="badge badge-green">' . esc($m['legal_status'] ?? 'Terverifikasi') . '</span>',
                        esc($m['phone'] ?? '-'),
                    ]
                ];
            }
        } elseif ($tab === 'jamaah' && $db->tableExists('jamaah')) {
            $headers = ['Nama Jamaah', 'NIK', 'Gender', 'No. HP', 'Status'];
            $data = $db->table('jamaah')->get()->getResultArray();
            foreach ($data as $j) {
                $rows[] = [
                    'columns' => [
                        '<strong>' . esc($j['full_name']) . '</strong>',
                        '<span class="stat-mono">' . esc($j['nik'] ?? '-') . '</span>',
                        esc($j['gender']),
                        esc($j['phone'] ?? '-'),
                        '<span class="badge badge-green">' . esc($j['status']) . '</span>',
                    ]
                ];
            }
        } elseif ($tab === 'family' && $db->tableExists('families')) {
            $headers = ['No. KK', 'Kepala Keluarga', 'Alamat', 'Tanggal Input'];
            $data = $db->table('families')->get()->getResultArray();
            foreach ($data as $f) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($f['family_card_number']) . '</span>',
                        '<strong>' . esc($f['head_of_family_name']) . '</strong>',
                        esc($f['address']),
                        esc(substr($f['created_at'], 0, 10)),
                    ]
                ];
            }
        } elseif ($tab === 'user' && $db->tableExists('users')) {
            $headers = ['Username', 'Nama Lengkap', 'Email', 'Status'];
            $data = $db->table('users')->get()->getResultArray();
            foreach ($data as $u) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($u['username']) . '</span>',
                        '<strong>' . esc($u['full_name']) . '</strong>',
                        esc($u['email']),
                        '<span class="badge badge-green">' . ($u['is_active'] ? 'ACTIVE' : 'INACTIVE') . '</span>',
                    ]
                ];
            }
        } elseif ($tab === 'role' && $db->tableExists('roles')) {
            $headers = ['ID', 'Nama Role', 'Deskripsi Role'];
            $data = $db->table('roles')->get()->getResultArray();
            foreach ($data as $r) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($r['id']) . '</span>',
                        '<strong>' . esc($r['name']) . '</strong>',
                        esc($r['description'] ?? '-'),
                    ]
                ];
            }
        } elseif ($tab === 'permission' && $db->tableExists('permissions')) {
            $headers = ['ID', 'Nama Permission', 'Deskripsi'];
            $data = $db->table('permissions')->get()->getResultArray();
            foreach ($data as $p) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($p['id']) . '</span>',
                        '<strong>' . esc($p['name']) . '</strong>',
                        esc($p['description'] ?? '-'),
                    ]
                ];
            }
        }

        $moduleLabels = [
            'profil'     => 'Profil & Identitas Masjid',
            'jamaah'     => 'Data Jamaah Masjid',
            'family'     => 'Data Kartu Keluarga',
            'user'       => 'Pengguna System (User)',
            'role'       => 'Hak Akses Role RBAC',
            'permission' => 'Daftar Permission',
        ];

        return view('admin/master/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Profil & Identitas Masjid',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }
}
