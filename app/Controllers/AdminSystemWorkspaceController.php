<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminSystemWorkspaceController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();

        $path = $this->request->getUri()->getPath();
        $defaultTab = 'settings';
        if (str_contains($path, 'menu')) {
            $defaultTab = 'menu';
        } elseif (str_contains($path, 'media')) {
            $defaultTab = 'media';
        } elseif (str_contains($path, 'notification')) {
            $defaultTab = 'notification';
        } elseif (str_contains($path, 'theme')) {
            $defaultTab = 'theme';
        }

        $tab = (string) ($this->request->getGet('tab') ?? $defaultTab);

        $headers = [];
        $rows = [];

        try {
            if ($tab === 'settings' && $db->tableExists('settings')) {
                $headers = ['Setting Key', 'Setting Value', 'Grup Konfigurasi', 'Aksi'];
                $data = $db->table('settings')->where('setting_group !=', 'theme')->get()->getResultArray();
                foreach ($data as $s) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($s['setting_key']) . '</span>',
                            '<strong>' . esc($s['setting_value']) . '</strong>',
                            '<span class="badge badge-green">' . esc($s['setting_group']) . '</span>',
                            '<a href="' . site_url('admin/settings/delete/' . esc($s['setting_key'])) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus setting ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'theme' && $db->tableExists('settings')) {
                // Theme owns ONLY appearance-related settings (color, layout,
                // mode). Mosque identity/logo/favicon/social/contact live in
                // Website Settings (admin/master?tab=profil) — see finding G.
                $themeRows = $db->table('settings')->where('setting_group', 'theme')->get()->getResultArray();
                $themeSettings = [];
                foreach ($themeRows as $s) {
                    $themeSettings[$s['setting_key']] = $s['setting_value'];
                }
                $data = $themeSettings;
            } elseif ($tab === 'menu' && $db->tableExists('menus')) {
                $headers = ['Nama Menu', 'URL / Target', 'Urutan (Sort)', 'Status', 'Aksi'];
                $data = $db->table('menus')->orderBy('menu_order', 'ASC')->get()->getResultArray();
                foreach ($data as $m) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($m['title'] ?? $m['name'] ?? '-') . '</strong>',
                            '<span class="stat-mono">' . esc($m['url'] ?? $m['target'] ?? '#') . '</span>',
                            '<span class="stat-mono">' . esc($m['menu_order'] ?? 0) . '</span>',
                            '<span class="badge badge-green">ACTIVE</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="' . site_url('admin/menu/edit/' . $m['id']) . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Edit</a>' .
                            '<a href="' . site_url('admin/menu/delete/' . $m['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus menu ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>' .
                            '</div>',
                        ]
                    ];
                }
            } elseif ($tab === 'media' && $db->tableExists('media')) {
                $headers = ['File Name', 'Tipe Media', 'Ukuran (Bytes)', 'Path File', 'Aksi'];
                $data = $db->table('media')->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $med) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($med['file_name'] ?? $med['filename'] ?? '-') . '</strong>',
                            '<span class="badge badge-green">' . esc($med['file_type'] ?? $med['mime_type'] ?? 'IMAGE') . '</span>',
                            '<span class="stat-mono">' . number_format((float)($med['file_size'] ?? 0)) . ' B</span>',
                            '<span class="stat-mono">' . esc($med['filepath'] ?? '-') . '</span>',
                            '<a href="' . site_url('admin/media/delete/' . $med['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus media ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'notification' && $db->tableExists('notifications')) {
                $headers = ['Judul Notifikasi', 'Penerima / User', 'Tipe Gateway', 'Status', 'Aksi'];
                $data = $db->table('notifications')->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $n) {
                    $rows[] = [
                        'columns' => [
                            '<strong>' . esc($n['title'] ?? '-') . '</strong>',
                            '<span class="stat-mono">' . esc($n['user_id'] ?? 'SEMUA') . '</span>',
                            '<span class="badge badge-green">' . esc($n['channel'] ?? 'SYSTEM') . '</span>',
                            '<span class="badge ' . (($n['is_read'] ?? 0) ? 'badge-gray' : 'badge-amber') . '">' . (($n['is_read'] ?? 0) ? 'READ' : 'UNREAD') . '</span>',
                            '<a href="' . site_url('admin/notification/delete/' . $n['id']) . '" class="btn btn-secondary" onclick="return confirm(\'Hapus notifikasi ini?\')" style="padding: 2px 8px; font-size: 12px; color: var(--status-danger-text);">Hapus</a>',
                        ]
                    ];
                }
            } elseif ($tab === 'audit' && $db->tableExists('audit_logs')) {
                $headers = ['User ID', 'Modul', 'Action', 'Alamat IP', 'Waktu'];
                $data = $db->table('audit_logs')->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($data as $a) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">USER-' . esc($a['user_id'] ?? 'SYSTEM') . '</span>',
                            '<strong>' . esc($a['module']) . '</strong>',
                            '<span class="badge badge-green">' . esc($a['action']) . '</span>',
                            '<span class="stat-mono">' . esc($a['ip_address']) . '</span>',
                            esc($a['created_at']),
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController Exception: ' . $e->getMessage());
        }

        $moduleLabels = [
            'settings'     => 'Pengaturan Platform System',
            'menu'         => 'Menu Navigation Manager',
            'media'        => 'Media & Asset Manager',
            'notification' => 'Notification & Gateway Manager',
            'audit'        => 'Audit Activity Log',
            'theme'        => 'Theme & Tampilan',
        ];

        return view('admin/system/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Pengaturan Platform System',
            'headers'           => $headers,
            'rows'              => $rows,
            'themeSettings'     => $themeSettings ?? [],
        ]);
    }

    public function store()
    {
        $db = Database::connect();
        try {
            $key = (string) $this->request->getPost('setting_key');
            $val = (string) $this->request->getPost('setting_value');
            $group = (string) ($this->request->getPost('setting_group') ?: 'general');

            if (!empty($key) && $db->tableExists('settings')) {
                $db->table('settings')->upsert([
                    'setting_key'   => $key,
                    'setting_value' => $val,
                    'setting_group' => $group,
                ]);
                session()->setFlashdata('success', 'Setting ' . esc($key) . ' berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController Store Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan Setting: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/settings?tab=settings'));
    }

    public function storeTheme()
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('settings')) {
                $fields = [
                    'theme_color_primary'  => (string) ($this->request->getPost('theme_color_primary') ?: '#16a34a'),
                    'theme_layout'         => (string) ($this->request->getPost('theme_layout') ?: 'default'),
                    'theme_appearance_mode' => (string) ($this->request->getPost('theme_appearance_mode') ?: 'light'),
                ];
                foreach ($fields as $key => $val) {
                    $db->table('settings')->upsert([
                        'setting_key'   => $key,
                        'setting_value' => $val,
                        'setting_group' => 'theme',
                    ]);
                }
                session()->setFlashdata('success', 'Pengaturan Tema berhasil disimpan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController StoreTheme Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan Tema: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/theme'));
    }

    public function deleteSetting(string $key)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('settings')) {
                $db->table('settings')->where('setting_key', $key)->delete();
                session()->setFlashdata('success', 'Setting ' . esc($key) . ' berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController DeleteSetting Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus Setting: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/settings?tab=settings'));
    }

    public function storeMenu()
    {
        $db = Database::connect();
        try {
            $title = (string) $this->request->getPost('title');
            $url = (string) $this->request->getPost('url');
            $sort = (int) ($this->request->getPost('sort_order') ?: 1);

            if (!empty($title) && $db->tableExists('menus')) {
                $menuData = [
                    'title'      => $title,
                    'url'        => $url,
                    'menu_order' => $sort,
                ];

                $db->table('menus')->insert($menuData);
                session()->setFlashdata('success', 'Menu ' . esc($title) . ' berhasil ditambahkan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController StoreMenu Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan Menu: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/menu?tab=menu'));
    }

    public function editMenu(string $id)
    {
        $db = Database::connect();
        $item = null;
        if ($db->tableExists('menus')) {
            $item = $db->table('menus')->where('id', $id)->get()->getRowArray();
        }

        if (!$item) {
            session()->setFlashdata('error', 'Menu tidak ditemukan di database.');
            return redirect()->to(site_url('admin/menu?tab=menu'));
        }

        return view('admin/system/menu_edit', [
            'id'   => $id,
            'item' => $item,
        ]);
    }

    public function updateMenu()
    {
        $db = Database::connect();
        $id = (string) $this->request->getPost('id');

        try {
            $title = (string) $this->request->getPost('title');
            $url = (string) $this->request->getPost('url');
            $sort = (int) ($this->request->getPost('sort_order') ?: 1);

            if (!empty($title) && $db->tableExists('menus')) {
                $db->table('menus')->where('id', $id)->update([
                    'title'      => $title,
                    'url'        => $url,
                    'menu_order' => $sort,
                ]);
                session()->setFlashdata('success', 'Menu ' . esc($title) . ' berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController UpdateMenu Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal memperbarui Menu: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/menu?tab=menu'));
    }

    public function deleteMenu(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('menus')) {
                $db->table('menus')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Menu berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController DeleteMenu Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus Menu: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/menu?tab=menu'));
    }

    public function storeMedia()
    {
        $db = Database::connect();
        try {
            $file = $this->request->getFile('media_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads', $newName);
                $filePath = 'uploads/' . $newName;

                if ($db->tableExists('media')) {
                    $mediaData = [
                        'filepath'   => $filePath,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    if ($db->fieldExists('file_name', 'media')) {
                        $mediaData['file_name'] = $file->getClientName();
                    } elseif ($db->fieldExists('filename', 'media')) {
                        $mediaData['filename'] = $file->getClientName();
                    }
                    if ($db->fieldExists('file_size', 'media')) {
                        $mediaData['file_size'] = $file->getSize();
                    }
                    if ($db->fieldExists('file_type', 'media')) {
                        $mediaData['file_type'] = $file->getClientMimeType();
                    }

                    $db->table('media')->insert($mediaData);
                    session()->setFlashdata('success', 'Media file ' . esc($file->getClientName()) . ' berhasil diunggah.');
                }
            } else {
                session()->setFlashdata('error', 'File tidak valid atau gagal diunggah.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController StoreMedia Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal mengunggah media: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/media?tab=media'));
    }

    public function deleteMedia(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('media')) {
                $db->table('media')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Media asset berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController DeleteMedia Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus media: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/media?tab=media'));
    }

    public function storeNotification()
    {
        $db = Database::connect();
        try {
            $title = (string) $this->request->getPost('title');
            $message = (string) $this->request->getPost('message');

            if (!empty($title) && $db->tableExists('notifications')) {
                $notifData = [
                    'title'      => $title,
                    'is_read'    => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                if ($db->fieldExists('message', 'notifications')) {
                    $notifData['message'] = $message;
                }
                if ($db->fieldExists('channel', 'notifications')) {
                    $notifData['channel'] = 'SYSTEM';
                }

                $db->table('notifications')->insert($notifData);
                session()->setFlashdata('success', 'Notifikasi ' . esc($title) . ' berhasil dikirim.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController StoreNotification Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/notification?tab=notification'));
    }

    public function deleteNotification(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('notifications')) {
                $db->table('notifications')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Notifikasi berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController DeleteNotification Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus notifikasi: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/notification?tab=notification'));
    }
}
