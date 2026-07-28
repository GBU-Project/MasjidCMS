<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminMasterDataController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        
        $path = $this->request->getUri()->getPath();
        $defaultTab = 'profil';
        if (str_contains($path, 'jamaah')) {
            $defaultTab = 'jamaah';
        } elseif (str_contains($path, 'family')) {
            $defaultTab = 'family';
        } elseif (str_contains($path, 'users')) {
            $defaultTab = 'user';
        } elseif (str_contains($path, 'rbac')) {
            $defaultTab = 'role';
        }

        $tab = (string) ($this->request->getGet('tab') ?? $defaultTab);

        $headers = [];
        $rows = [];

        try {
            if ($tab === 'profil' && $db->tableExists('masjids')) {
                $headers = ['Nama Masjid', 'Kode Scope', 'Kota / Lokasi', 'Status Legalitas', 'Telepon'];
                $data = $db->table('masjids')->get()->getResultArray();
                foreach ($data as $m) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($m['name']) . '</strong>',
                            '<span class="stat-mono">' . esc($m['code']) . '</span>',
                            esc($m['city'] ?? '-'),
                            '<span class="badge badge-green">' . esc($m['status'] ?? $m['legal_status'] ?? 'Terverifikasi') . '</span>',
                            esc($m['phone'] ?? '-'),
                        ]
                    ];
                }
            } elseif ($tab === 'jamaah' && $db->tableExists('jamaahs')) {
                $headers = ['Nama Jamaah', 'NIK', 'Gender', 'No. HP', 'Status'];
                $data = $db->table('jamaahs')->get()->getResultArray();
                foreach ($data as $j) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($j['full_name']) . '</strong>',
                            '<span class="stat-mono">' . esc($j['nik'] ?? '-') . '</span>',
                            esc($j['gender'] ?? '-'),
                            esc($j['phone'] ?? '-'),
                            '<span class="badge badge-green">' . esc($j['status'] ?? 'ACTIVE') . '</span>',
                        ]
                    ];
                }
            } elseif ($tab === 'family' && $db->tableExists('families')) {
                $headers = ['No. KK', 'Kepala Keluarga', 'Alamat', 'Tanggal Input'];
                $builder = $db->table('families');
                if ($db->tableExists('jamaahs') && $db->fieldExists('head_jamaah_id', 'families')) {
                    $builder->select('families.*, jamaahs.full_name as head_name')
                            ->join('jamaahs', 'jamaahs.id = families.head_jamaah_id', 'left');
                }
                $data = $builder->get()->getResultArray();
                foreach ($data as $f) {
                    $kkNo = $f['kk_number'] ?? $f['family_card_number'] ?? $f['family_no'] ?? '-';
                    $headName = $f['head_name'] ?? $f['head_of_family_name'] ?? $f['name'] ?? '-';
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($kkNo) . '</span>',
                            '<strong>' . esc($headName) . '</strong>',
                            esc($f['address'] ?? '-'),
                            esc(substr($f['created_at'] ?? date('Y-m-d'), 0, 10)),
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
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Exception: ' . $e->getMessage());
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
