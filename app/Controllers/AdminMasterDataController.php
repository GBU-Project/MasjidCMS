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
                $headers = ['Nama Masjid', 'Kode Scope', 'Kota / Lokasi', 'Status Legalitas', 'Telepon', 'Aksi'];
                $data = $db->table('masjids')->get()->getResultArray();
                foreach ($data as $m) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($m['name']) . '</strong>',
                            '<span class="stat-mono">' . esc($m['code']) . '</span>',
                            esc($m['city'] ?? '-'),
                            '<span class="badge badge-green">' . esc($m['status'] ?? $m['legal_status'] ?? 'Terverifikasi') . '</span>',
                            esc($m['phone'] ?? '-'),
                            '<a href="/admin/master/delete/masjids/' . $m['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus profil masjid ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'jamaah' && $db->tableExists('jamaahs')) {
                $headers = ['Nama Jamaah', 'NIK', 'Gender', 'No. HP', 'Status', 'Aksi'];
                $data = $db->table('jamaahs')->get()->getResultArray();
                foreach ($data as $j) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($j['full_name']) . '</strong>',
                            '<span class="stat-mono">' . esc($j['nik'] ?? '-') . '</span>',
                            esc($j['gender'] ?? '-'),
                            esc($j['phone'] ?? '-'),
                            '<span class="badge badge-green">' . esc($j['status'] ?? 'ACTIVE') . '</span>',
                            '<a href="/admin/master/delete/jamaahs/' . $j['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus jamaah ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'family' && $db->tableExists('families')) {
                $headers = ['No. KK', 'Kepala Keluarga', 'Alamat', 'Tanggal Input', 'Aksi'];
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
                            '<a href="/admin/master/delete/families/' . $f['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus keluarga ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'user' && $db->tableExists('users')) {
                $headers = ['Username', 'Nama Lengkap', 'Email', 'Status', 'Aksi'];
                $data = $db->table('users')->get()->getResultArray();
                foreach ($data as $u) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($u['username']) . '</span>',
                            '<strong>' . esc($u['full_name']) . '</strong>',
                            esc($u['email']),
                            '<span class="badge badge-green">' . ($u['is_active'] ? 'ACTIVE' : 'INACTIVE') . '</span>',
                            '<a href="/admin/master/delete/users/' . $u['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus user ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'role' && $db->tableExists('roles')) {
                $headers = ['ID', 'Nama Role', 'Deskripsi Role', 'Aksi'];
                $data = $db->table('roles')->get()->getResultArray();
                foreach ($data as $r) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($r['id']) . '</span>',
                            '<strong>' . esc($r['name']) . '</strong>',
                            esc($r['description'] ?? '-'),
                            '<a href="/admin/master/delete/roles/' . $r['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus role ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'permission' && $db->tableExists('permissions')) {
                $headers = ['ID', 'Nama Permission', 'Deskripsi', 'Aksi'];
                $data = $db->table('permissions')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($p['id']) . '</span>',
                            '<strong>' . esc($p['name']) . '</strong>',
                            esc($p['description'] ?? '-'),
                            '<a href="/admin/master/delete/permissions/' . $p['id'] . '" class="btn btn-secondary" onclick="return confirm(\'Hapus permission ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
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

    public function create(): string
    {
        $tab = (string) ($this->request->getGet('tab') ?? 'jamaah');
        return view('admin/master/create', [
            'tab' => $tab,
        ]);
    }

    public function store()
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getPost('tab') ?? 'jamaah');

        try {
            if ($tab === 'profil') {
                $db->table('masjids')->insert([
                    'code'       => (string) $this->request->getPost('code'),
                    'name'       => (string) $this->request->getPost('name'),
                    'slug'       => url_title((string) $this->request->getPost('name'), '-', true),
                    'city'       => (string) $this->request->getPost('city'),
                    'phone'      => (string) $this->request->getPost('phone'),
                    'email'      => (string) $this->request->getPost('email'),
                    'address'    => (string) $this->request->getPost('address'),
                    'status'     => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'jamaah') {
                $fullName = (string) $this->request->getPost('full_name');
                $nik = (string) $this->request->getPost('nik');
                $gender = (string) $this->request->getPost('gender');
                $phone = (string) $this->request->getPost('phone');
                $address = (string) $this->request->getPost('address');

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $memberNo = 'JM-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

                $db->table('jamaahs')->insert([
                    'id'         => $uuid,
                    'member_no'  => $memberNo,
                    'nik'        => $nik,
                    'full_name'  => $fullName,
                    'gender'     => $gender,
                    'phone'      => $phone,
                    'address'    => $address,
                    'status'     => 'ACTIVE',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'family') {
                $kkNumber = (string) $this->request->getPost('kk_number');
                $name = (string) $this->request->getPost('name');
                $address = (string) $this->request->getPost('address');

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $familyNo = 'KK-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

                $db->table('families')->insert([
                    'id'            => $uuid,
                    'family_no'     => $familyNo,
                    'kk_number'     => $kkNumber,
                    'name'          => $name,
                    'address'       => $address,
                    'family_status' => 'ACTIVE',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'user') {
                $username = (string) $this->request->getPost('username');
                $fullName = (string) $this->request->getPost('full_name');
                $email = (string) $this->request->getPost('email');
                $password = (string) $this->request->getPost('password');

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

                $db->table('users')->insert([
                    'uuid'          => $uuid,
                    'username'      => $username,
                    'full_name'     => $fullName,
                    'email'         => $email,
                    'password_hash' => password_hash($password ?: 'Secret123!', PASSWORD_BCRYPT),
                    'is_active'     => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'role') {
                $name = (string) $this->request->getPost('name');
                $desc = (string) $this->request->getPost('description');

                $db->table('roles')->insert([
                    'name'        => $name,
                    'description' => $desc,
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            } elseif ($tab === 'permission') {
                $name = (string) $this->request->getPost('name');
                $desc = (string) $this->request->getPost('description');

                $db->table('permissions')->insert([
                    'name'        => $name,
                    'description' => $desc,
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Store Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/master?tab=' . $tab));
    }

    public function delete(string $type, string $id)
    {
        $db = Database::connect();
        try {
            $allowed = ['masjids', 'jamaahs', 'families', 'users', 'roles', 'permissions'];
            if (in_array($type, $allowed, true) && $db->tableExists($type)) {
                $db->table($type)->where('id', $id)->delete();
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Delete Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/master?tab=' . $type));
    }
}
