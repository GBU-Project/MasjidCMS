<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminMasterDataController extends BaseController
{
    /**
     * Standard action-column markup used across all Master Data tabs.
     * Only Edit & Delete are rendered because those are the only actions
     * with a real, working implementation. View/History are intentionally
     * omitted (TASK-018) until those features actually exist — no dummy
     * buttons.
     */
    private function actionButtons(string $editUrl, string $deleteUrl, string $confirmMessage): string
    {
        $confirm = esc($confirmMessage, 'js');

        return '<div class="row-actions" style="display:flex; gap:4px; justify-content:flex-end;">'
            . '<a href="' . esc($editUrl) . '" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Edit Data">✏️</a>'
            . '<a href="' . esc($deleteUrl) . '" class="btn btn-secondary" onclick="return confirm(\'' . $confirm . '\')" style="padding: 4px 8px; font-size: 12px; color: var(--status-danger-text);" title="Hapus Data">🗑️</a>'
            . '</div>';
    }

    public function index(): string
    {
        $db = Database::connect();
        
        $path = $this->request->getUri()->getPath();
        $defaultTab = 'profil';
        if (str_contains($path, 'bidang')) {
            $defaultTab = 'bidang';
        } elseif (str_contains($path, 'pengurus')) {
            $defaultTab = 'pengurus';
        } elseif (str_contains($path, 'jamaah')) {
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
                            $this->actionButtons(
                                site_url('admin/master/edit/profil/' . $m['id']),
                                site_url('admin/master/delete/masjids/' . $m['id']),
                                'Hapus profil masjid ini?'
                            ),
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
                            $this->actionButtons(
                                site_url('admin/master/edit/jamaah/' . $j['id']),
                                site_url('admin/master/delete/jamaahs/' . $j['id']),
                                'Hapus jamaah ini?'
                            ),
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
                            $this->actionButtons(
                                site_url('admin/master/edit/family/' . $f['id']),
                                site_url('admin/master/delete/families/' . $f['id']),
                                'Hapus keluarga ini?'
                            ),
                        ]
                    ];
                }
            } elseif ($tab === 'user' && $db->tableExists('users')) {
                $headers = ['Username', 'Nama Lengkap', 'Email', 'Status', 'Aksi'];
                $data = $db->table('users')->get()->getResultArray();
                foreach ($data as $u) {
                    $fullName = $u['full_name'] ?? $u['username'];
                    $isActive = isset($u['is_active']) ? $u['is_active'] : ($u['status'] === 'ACTIVE' ? 1 : 0);
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($u['username']) . '</span>',
                            '<strong>' . esc($fullName) . '</strong>',
                            esc($u['email']),
                            '<span class="badge badge-green">' . ($isActive ? 'ACTIVE' : 'INACTIVE') . '</span>',
                            $this->actionButtons(
                                site_url('admin/master/edit/user/' . $u['id']),
                                site_url('admin/master/delete/users/' . $u['id']),
                                'Hapus user ini?'
                            ),
                        ]
                    ];
                }
            } elseif ($tab === 'role' && $db->tableExists('roles')) {
                $headers = ['ID', 'Nama Role', 'Deskripsi Role', 'Aksi'];
                $data = $db->table('roles')->get()->getResultArray();
                foreach ($data as $r) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc(substr($r['id'], 0, 8)) . '...</span>',
                            '<strong>' . esc($r['name']) . '</strong>',
                            esc($r['description'] ?? '-'),
                            $this->actionButtons(
                                site_url('admin/master/edit/role/' . $r['id']),
                                site_url('admin/master/delete/roles/' . $r['id']),
                                'Hapus role ini?'
                            ),
                        ]
                    ];
                }
            } elseif ($tab === 'bidang' && $db->tableExists('bidang')) {
                $headers = ['Nama Bidang', 'Icon', 'Deskripsi', 'Urutan', 'Status', 'Aksi'];
                $data = $db->table('bidang')->where('deleted_at', null)->orderBy('sort_order', 'ASC')->get()->getResultArray();
                foreach ($data as $b) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($b['name']) . '</strong>',
                            '<span style="font-size: 18px;">' . esc($b['icon'] ?? '🏛️') . '</span>',
                            esc($b['description'] ?? '-'),
                            '<span class="stat-mono">' . esc($b['sort_order'] ?? 1) . '</span>',
                            '<span class="badge badge-green">' . esc($b['status'] ?? 'ACTIVE') . '</span>',
                            $this->actionButtons(
                                site_url('admin/master/edit/bidang/' . $b['id']),
                                site_url('admin/master/delete/bidang/' . $b['id']),
                                'Hapus bidang ini?'
                            ),
                        ]
                    ];
                }
            } elseif ($tab === 'pengurus' && $db->tableExists('pengurus')) {
                $headers = ['Nama Pengurus', 'Jabatan', 'Bidang', 'No. HP', 'Status', 'Aksi'];
                $builder = $db->table('pengurus');
                if ($db->tableExists('bidang')) {
                    $builder->select('pengurus.*, bidang.name as bidang_name')
                            ->join('bidang', 'bidang.id = pengurus.bidang_id', 'left');
                }
                $data = $builder->orderBy('pengurus.urutan', 'ASC')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($p['nama']) . '</strong>',
                            '<span class="badge badge-blue">' . esc($p['jabatan']) . '</span>',
                            esc($p['bidang_name'] ?? '-'),
                            esc($p['telepon'] ?? '-'),
                            '<span class="badge badge-green">' . esc($p['status'] ?? 'ACTIVE') . '</span>',
                            $this->actionButtons(
                                site_url('admin/master/edit/pengurus/' . $p['id']),
                                site_url('admin/master/delete/pengurus/' . $p['id']),
                                'Hapus pengurus ini?'
                            ),
                        ]
                    ];
                }
            } elseif ($tab === 'permission' && $db->tableExists('permissions')) {
                $headers = ['ID', 'Nama Permission', 'Deskripsi', 'Aksi'];
                $data = $db->table('permissions')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc(substr($p['id'], 0, 8)) . '...</span>',
                            '<strong>' . esc($p['name'] ?? $p['permission_code'] ?? '-') . '</strong>',
                            esc($p['description'] ?? '-'),
                            $this->actionButtons(
                                site_url('admin/master/edit/permission/' . $p['id']),
                                site_url('admin/master/delete/permissions/' . $p['id']),
                                'Hapus permission ini?'
                            ),
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Exception: ' . $e->getMessage());
        }

        $moduleLabels = [
            'profil'     => 'Profil & Identitas Masjid',
            'bidang'     => 'Data Bidang / Departemen',
            'pengurus'   => 'Data Pengurus Masjid',
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
                $rules = ['code' => 'required', 'name' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Profil Masjid: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $db->table('masjids')->insert([
                    'code'          => (string) $this->request->getPost('code'),
                    'name'          => (string) $this->request->getPost('name'),
                    'slug'          => url_title((string) $this->request->getPost('name'), '-', true),
                    'city'          => (string) $this->request->getPost('city'),
                    'phone'         => (string) $this->request->getPost('phone'),
                    'email'         => (string) $this->request->getPost('email'),
                    'address'       => (string) $this->request->getPost('address'),
                    'history_text'  => (string) $this->request->getPost('history_text'),
                    'vision_text'   => (string) $this->request->getPost('vision_text'),
                    'mission_text'  => (string) $this->request->getPost('mission_text'),
                    'status'        => 'active',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Profil Masjid berhasil disimpan.');

            } elseif ($tab === 'jamaah') {
                $rules = ['full_name' => 'required', 'gender' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Jamaah: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $memberNo = 'JM-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

                $db->table('jamaahs')->insert([
                    'id'         => $uuid,
                    'member_no'  => $memberNo,
                    'nik'        => (string) $this->request->getPost('nik'),
                    'full_name'  => (string) $this->request->getPost('full_name'),
                    'gender'     => (string) $this->request->getPost('gender'),
                    'phone'      => (string) $this->request->getPost('phone'),
                    'address'    => (string) $this->request->getPost('address'),
                    'status'     => 'ACTIVE',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Data Jamaah berhasil disimpan.');

            } elseif ($tab === 'family') {
                $rules = ['kk_number' => 'required', 'name' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Kartu Keluarga: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $familyNo = 'KK-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

                $db->table('families')->insert([
                    'id'            => $uuid,
                    'family_no'     => $familyNo,
                    'kk_number'     => (string) $this->request->getPost('kk_number'),
                    'name'          => (string) $this->request->getPost('name'),
                    'address'       => (string) $this->request->getPost('address'),
                    'family_status' => 'ACTIVE',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Data Kartu Keluarga berhasil disimpan.');

            } elseif ($tab === 'user') {
                $rules = ['username' => 'required', 'email' => 'required|valid_email'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan User: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $password = (string) $this->request->getPost('password');

                $userData = [
                    'id'            => $uuid,
                    'username'      => (string) $this->request->getPost('username'),
                    'email'         => (string) $this->request->getPost('email'),
                    'password_hash' => password_hash($password ?: 'Secret123!', PASSWORD_BCRYPT),
                    'status'        => 'ACTIVE',
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
                if ($db->fieldExists('full_name', 'users')) {
                    $userData['full_name'] = (string) $this->request->getPost('full_name');
                }
                if ($db->fieldExists('is_active', 'users')) {
                    $userData['is_active'] = 1;
                }

                $db->table('users')->insert($userData);
                session()->setFlashdata('success', 'User Account berhasil disimpan.');

            } elseif ($tab === 'role') {
                $rules = ['name' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Role: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $name = (string) $this->request->getPost('name');

                $roleData = [
                    'id'          => $uuid,
                    'role_code'   => strtoupper(url_title($name, '_', true)),
                    'name'        => $name,
                    'description' => (string) $this->request->getPost('description'),
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                $db->table('roles')->insert($roleData);
                session()->setFlashdata('success', 'Role RBAC berhasil disimpan.');

            } elseif ($tab === 'bidang') {
                $rules = ['name' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Bidang: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $name = (string) $this->request->getPost('name');
                $db->table('bidang')->insert([
                    'name'        => $name,
                    'slug'        => url_title($name, '-', true),
                    'icon'        => (string) ($this->request->getPost('icon') ?: '🏛️'),
                    'description' => (string) $this->request->getPost('description'),
                    'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
                    'status'      => 'ACTIVE',
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Data Bidang ' . esc($name) . ' berhasil disimpan.');

            } elseif ($tab === 'pengurus') {
                $rules = ['nama' => 'required', 'jabatan' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Pengurus: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $nama = (string) $this->request->getPost('nama');
                $db->table('pengurus')->insert([
                    'bidang_id'     => (int) ($this->request->getPost('bidang_id') ?: null),
                    'nama'          => $nama,
                    'jabatan'       => (string) $this->request->getPost('jabatan'),
                    'jenis_kelamin' => (string) ($this->request->getPost('jenis_kelamin') ?: 'L'),
                    'telepon'       => (string) $this->request->getPost('telepon'),
                    'email'         => (string) $this->request->getPost('email'),
                    'alamat'        => (string) $this->request->getPost('alamat'),
                    'bio'           => (string) $this->request->getPost('bio'),
                    'urutan'        => (int) ($this->request->getPost('urutan') ?: 1),
                    'status'        => 'ACTIVE',
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Data Pengurus ' . esc($nama) . ' berhasil disimpan.');

            } elseif ($tab === 'permission') {
                $rules = ['name' => 'required'];
                if (!$this->validate($rules)) {
                    session()->setFlashdata('error', 'Gagal menyimpan Permission: ' . implode(', ', $this->validator->getErrors()));
                    return redirect()->back()->withInput();
                }

                $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
                $name = (string) $this->request->getPost('name');

                $permData = [
                    'id'              => $uuid,
                    'permission_code' => strtolower(url_title($name, '.', true)),
                    'module_name'     => 'system',
                    'description'     => (string) $this->request->getPost('description'),
                    'created_at'      => date('Y-m-d H:i:s'),
                ];
                if ($db->fieldExists('name', 'permissions')) {
                    $permData['name'] = $name;
                }

                $db->table('permissions')->insert($permData);
                session()->setFlashdata('success', 'Permission RBAC berhasil disimpan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Store Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan Master Data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to(site_url('admin/master?tab=' . $tab));
    }

    public function edit(string $type, string $id)
    {
        $db = Database::connect();
        $tableMap = [
            'profil'     => 'masjids',
            'bidang'     => 'bidang',
            'pengurus'   => 'pengurus',
            'jamaah'     => 'jamaahs',
            'family'     => 'families',
            'user'       => 'users',
            'role'       => 'roles',
            'permission' => 'permissions',
        ];

        $tableName = $tableMap[$type] ?? $type;
        $item = null;

        if ($db->tableExists($tableName)) {
            $item = $db->table($tableName)->where('id', $id)->get()->getRowArray();
        }

        if (!$item) {
            session()->setFlashdata('error', 'Data Master tidak ditemukan di database.');
            return redirect()->to(site_url('admin/master?tab=' . $type));
        }

        return view('admin/master/edit', [
            'tab'  => $type,
            'id'   => $id,
            'item' => $item,
        ]);
    }

    public function update()
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getPost('tab') ?? 'jamaah');
        $id = (string) $this->request->getPost('id');

        try {
            if ($tab === 'profil') {
                $profileData = [
                    'code'    => (string) $this->request->getPost('code'),
                    'name'    => (string) $this->request->getPost('name'),
                    'city'    => (string) $this->request->getPost('city'),
                    'phone'   => (string) $this->request->getPost('phone'),
                    'email'   => (string) $this->request->getPost('email'),
                    'address' => (string) $this->request->getPost('address'),
                ];

                // Defensive: only write these columns if the migration that
                // adds them has actually run on this database.
                foreach (['history_text', 'vision_text', 'mission_text'] as $narrativeField) {
                    if ($db->fieldExists($narrativeField, 'masjids')) {
                        $profileData[$narrativeField] = (string) $this->request->getPost($narrativeField);
                    }
                }

                $db->table('masjids')->where('id', $id)->update($profileData);
                session()->setFlashdata('success', 'Profil Masjid berhasil diperbarui.');

            } elseif ($tab === 'bidang') {
                $db->table('bidang')->where('id', $id)->update([
                    'name'        => (string) $this->request->getPost('name'),
                    'icon'        => (string) $this->request->getPost('icon'),
                    'description' => (string) $this->request->getPost('description'),
                    'sort_order'  => (int) $this->request->getPost('sort_order'),
                ]);
                session()->setFlashdata('success', 'Data Bidang berhasil diperbarui.');

            } elseif ($tab === 'pengurus') {
                $db->table('pengurus')->where('id', $id)->update([
                    'nama'          => (string) $this->request->getPost('nama'),
                    'jabatan'       => (string) $this->request->getPost('jabatan'),
                    'bidang_id'     => (int) $this->request->getPost('bidang_id'),
                    'jenis_kelamin' => (string) $this->request->getPost('jenis_kelamin'),
                    'telepon'       => (string) $this->request->getPost('telepon'),
                    'email'         => (string) $this->request->getPost('email'),
                    'alamat'        => (string) $this->request->getPost('alamat'),
                    'bio'           => (string) $this->request->getPost('bio'),
                ]);
                session()->setFlashdata('success', 'Data Pengurus berhasil diperbarui.');

            } elseif ($tab === 'jamaah') {
                $db->table('jamaahs')->where('id', $id)->update([
                    'nik'       => (string) $this->request->getPost('nik'),
                    'full_name' => (string) $this->request->getPost('full_name'),
                    'gender'    => (string) $this->request->getPost('gender'),
                    'phone'     => (string) $this->request->getPost('phone'),
                    'address'   => (string) $this->request->getPost('address'),
                ]);
                session()->setFlashdata('success', 'Data Jamaah berhasil diperbarui.');

            } elseif ($tab === 'family') {
                $db->table('families')->where('id', $id)->update([
                    'kk_number' => (string) $this->request->getPost('kk_number'),
                    'name'      => (string) $this->request->getPost('name'),
                    'address'   => (string) $this->request->getPost('address'),
                ]);
                session()->setFlashdata('success', 'Data Kartu Keluarga berhasil diperbarui.');

            } elseif ($tab === 'user') {
                $userData = [
                    'username' => (string) $this->request->getPost('username'),
                    'email'    => (string) $this->request->getPost('email'),
                ];
                if ($db->fieldExists('full_name', 'users')) {
                    $userData['full_name'] = (string) $this->request->getPost('full_name');
                }
                $pwd = (string) $this->request->getPost('password');
                if (!empty($pwd)) {
                    $userData['password_hash'] = password_hash($pwd, PASSWORD_BCRYPT);
                }

                $db->table('users')->where('id', $id)->update($userData);
                session()->setFlashdata('success', 'User Account berhasil diperbarui.');

            } elseif ($tab === 'role') {
                $db->table('roles')->where('id', $id)->update([
                    'name'        => (string) $this->request->getPost('name'),
                    'description' => (string) $this->request->getPost('description'),
                ]);
                session()->setFlashdata('success', 'Role RBAC berhasil diperbarui.');

            } elseif ($tab === 'permission') {
                $permData = [
                    'description' => (string) $this->request->getPost('description'),
                ];
                if ($db->fieldExists('name', 'permissions')) {
                    $permData['name'] = (string) $this->request->getPost('name');
                }
                $db->table('permissions')->where('id', $id)->update($permData);
                session()->setFlashdata('success', 'Permission RBAC berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Update Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memperbarui data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to(site_url('admin/master?tab=' . $tab));
    }

    public function delete(string $type, string $id)
    {
        $db = Database::connect();
        try {
            $allowed = ['masjids', 'bidang', 'pengurus', 'jamaahs', 'families', 'users', 'roles', 'permissions'];
            if (in_array($type, $allowed, true) && $db->tableExists($type)) {
                $db->table($type)->where('id', $id)->delete();
                session()->setFlashdata('success', 'Data Master ' . esc($type) . ' berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminMasterDataController Delete Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus data Master: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/master?tab=' . $type));
    }
}
