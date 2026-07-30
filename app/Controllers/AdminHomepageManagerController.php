<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminHomepageManagerController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $db = Database::connect();

        // 1. Fetch settings
        $settings = [];
        if ($db->tableExists('settings')) {
            $raw = $db->table('settings')->get()->getResultArray();
            foreach ($raw as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        // Default section order
        $defaultOrder = ['hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'bidang', 'kajian', 'agenda', 'gallery', 'donation'];
        $sectionOrder = $defaultOrder;
        if (!empty($settings['homepage_section_order'])) {
            $decoded = json_decode($settings['homepage_section_order'], true);
            if (is_array($decoded) && count($decoded) > 0) {
                $sectionOrder = $decoded;
            }
        }

        // Section Metadata Definition
        $sectionMeta = [
            'hero' => ['name' => 'Hero Banner Header', 'icon' => '🚀', 'table' => null],
            'prayer' => ['name' => 'Jadwal Sholat Widget', 'icon' => '🕌', 'table' => null],
            'profile' => ['name' => 'Profil & Identitas Masjid', 'icon' => '🏛️', 'table' => 'masjids', 'setting_key' => 'show_profile_section'],
            'program' => ['name' => 'Program & Kegiatan Masjid', 'icon' => '🚩', 'table' => 'program_kegiatan', 'setting_key' => 'show_program_section', 'limit_key' => 'limit_program'],
            'layanan' => ['name' => 'Katalog Layanan Masjid', 'icon' => '🤝', 'table' => 'layanan_masjid', 'setting_key' => 'show_layanan_section', 'limit_key' => 'limit_layanan'],
            'pengurus' => ['name' => 'Pengurus DKM Masjid', 'icon' => '👔', 'table' => 'pengurus', 'setting_key' => 'show_pengurus_section', 'limit_key' => 'limit_pengurus'],
            'bidang' => ['name' => 'Bidang / Departemen', 'icon' => '🏛️', 'table' => 'bidang', 'setting_key' => 'show_bidang_section', 'limit_key' => 'limit_bidang'],
            'kajian' => ['name' => 'Jadwal Warta & Kajian', 'icon' => '📖', 'table' => 'kajian', 'setting_key' => 'show_kajian_section', 'limit_key' => 'limit_kajian'],
            'agenda' => ['name' => 'Agenda & Jadwal Kegiatan', 'icon' => '📅', 'table' => 'agenda', 'setting_key' => 'show_agenda_section', 'limit_key' => 'limit_agenda'],
            'gallery' => ['name' => 'Galeri Foto & Dokumentasi', 'icon' => '🖼️', 'table' => 'gallery', 'setting_key' => 'show_gallery_section', 'limit_key' => 'limit_gallery'],
            'donation' => ['name' => 'Donasi & Infaq Online', 'icon' => '💰', 'table' => 'financial_accounts', 'setting_key' => 'show_donation_section'],
        ];

        // 2. Compute Summary Statistics per Section
        $sectionStats = [];
        foreach ($sectionMeta as $secKey => $meta) {
            $activeCount = 0;
            $featuredCount = 0;
            $hiddenCount = 0;
            $totalCount = 0;

            if ($meta['table'] && $db->tableExists($meta['table'])) {
                $totalCount = $db->table($meta['table'])->countAllResults();

                $builderActive = $db->table($meta['table']);
                if ($db->fieldExists('status', $meta['table'])) {
                    $builderActive->where('status', 'ACTIVE');
                } elseif ($db->fieldExists('is_active', $meta['table'])) {
                    $builderActive->where('is_active', 1);
                }
                $activeCount = $builderActive->countAllResults();

                if ($db->fieldExists('featured', $meta['table'])) {
                    $featuredCount = $db->table($meta['table'])->where('featured', 1)->countAllResults();
                }

                $hiddenCount = $totalCount - $activeCount;
            } else {
                $activeCount = 1;
                $totalCount = 1;
            }

            $isVisible = true;
            if (isset($meta['setting_key'])) {
                $isVisible = ($settings[$meta['setting_key']] ?? '1') === '1';
            }

            $statusBadge = 'Visible';
            if (!$isVisible) {
                $statusBadge = 'Hidden';
            } elseif ($totalCount === 0) {
                $statusBadge = 'No Content';
            } elseif ($featuredCount > 0) {
                $statusBadge = 'Featured';
            }

            $sectionStats[$secKey] = [
                'meta'          => $meta,
                'active'        => $activeCount,
                'featured'      => $featuredCount,
                'hidden'        => $hiddenCount,
                'total'         => $totalCount,
                'is_visible'     => $isVisible,
                'status_badge'  => $statusBadge,
                'limit'         => (int) ($settings[$meta['limit_key'] ?? ''] ?? 6),
            ];
        }

        return view('admin/homepage/index', [
            'activePage'   => 'homepage-manager',
            'sectionOrder' => $sectionOrder,
            'sectionStats' => $sectionStats,
            'settings'     => $settings,
        ]);
    }

    public function saveOrder()
    {
        $db = Database::connect();
        $orderInput = $this->request->getPost('section_order');
        
        if (is_string($orderInput)) {
            $orderArr = json_decode($orderInput, true) ?? explode(',', $orderInput);
        } else {
            $orderArr = (array) $orderInput;
        }

        $cleanOrder = array_unique(array_filter($orderArr));
        if (count($cleanOrder) === 0) {
            return redirect()->back()->with('error', 'Urutan section tidak boleh kosong.');
        }

        $this->saveSettingKey('homepage_section_order', json_encode(array_values($cleanOrder)));
        $this->logActivity('Homepage order changed', 'Pengurus memperbarui urutan section homepage.');
        $this->purgeCache();

        return redirect()->back()->with('success', 'Urutan section Homepage berhasil diperbarui!');
    }

    public function saveSettings()
    {
        $db = Database::connect();
        $postData = $this->request->getPost();

        // Validation for limits
        foreach ($postData as $key => $value) {
            if (str_starts_with($key, 'limit_')) {
                $valInt = (int) $value;
                if ($valInt < 1 || $valInt > 100) {
                    return redirect()->back()->with('error', 'Limit item harus antara 1 dan 100.');
                }
            }
            if (str_starts_with($key, 'show_') || str_starts_with($key, 'limit_') || str_starts_with($key, 'donation_')) {
                $this->saveSettingKey($key, (string) $value);
            }
        }

        $this->logActivity('Homepage limit changed', 'Pengurus meng-update limit item dan visibilitas homepage.');
        $this->purgeCache();

        return redirect()->back()->with('success', 'Konfigurasi Homepage berhasil disimpan!');
    }

    public function bulkAction()
    {
        $action = (string) $this->request->getPost('action');
        $db = Database::connect();

        if ($action === 'show_all') {
            $keys = ['show_pengurus_section', 'show_program_section', 'show_layanan_section', 'show_bidang_section', 'show_kajian_section', 'show_agenda_section', 'show_gallery_section', 'show_profile_section', 'show_donation_section'];
            foreach ($keys as $k) {
                $this->saveSettingKey($k, '1');
            }
            $this->logActivity('Homepage section shown', 'Semua section homepage di-set Tampil.');
        } elseif ($action === 'hide_all') {
            $keys = ['show_pengurus_section', 'show_program_section', 'show_layanan_section', 'show_bidang_section', 'show_kajian_section', 'show_agenda_section', 'show_gallery_section', 'show_profile_section', 'show_donation_section'];
            foreach ($keys as $k) {
                $this->saveSettingKey($k, '0');
            }
            $this->logActivity('Homepage section hidden', 'Semua section homepage di-set Sembunyi.');
        } elseif ($action === 'enable_featured') {
            $tables = ['program_kegiatan', 'layanan_masjid', 'pengurus', 'bidang'];
            foreach ($tables as $t) {
                if ($db->tableExists($t) && $db->fieldExists('featured', $t)) {
                    $db->table($t)->update(['featured' => 1]);
                }
            }
            $this->logActivity('Homepage featured updated', 'Semua item di-set sebagai Featured.');
        } elseif ($action === 'disable_featured' || $action === 'reset_featured') {
            $tables = ['program_kegiatan', 'layanan_masjid', 'pengurus', 'bidang'];
            foreach ($tables as $t) {
                if ($db->tableExists($t) && $db->fieldExists('featured', $t)) {
                    $db->table($t)->update(['featured' => 0]);
                }
            }
            $this->logActivity('Homepage featured updated', 'Reset status Featured seluruh item.');
        }

        $this->purgeCache();
        return redirect()->back()->with('success', 'Aksi massal (Bulk Action) berhasil dieksekusi!');
    }

    public function resetDefault()
    {
        $defaultOrder = ['hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'bidang', 'kajian', 'agenda', 'gallery', 'donation'];
        $this->saveSettingKey('homepage_section_order', json_encode($defaultOrder));

        $defaultLimits = [
            'show_pengurus_section' => '1',
            'show_program_section'  => '1',
            'show_layanan_section'  => '1',
            'show_bidang_section'   => '1',
            'limit_pengurus'        => '3',
            'limit_bidang'          => '6',
            'limit_program'         => '6',
            'limit_bidang'          => '6',
            'limit_kajian'          => '6',
            'limit_gallery'         => '8',
            'limit_layanan'         => '4',
            'limit_agenda'          => '5',
        ];

        foreach ($defaultLimits as $k => $v) {
            $this->saveSettingKey($k, $v);
        }

        $this->logActivity('Homepage order reset', 'Urutan dan konfigurasi homepage dikembalikan ke sistem standar.');
        $this->purgeCache();

        return redirect()->back()->with('success', 'Konfigurasi Homepage berhasil di-reset ke nilai standar bawaan!');
    }

    public function clearCache()
    {
        $this->purgeCache();
        $this->logActivity('Homepage cache cleared', 'Tembolok (cache) homepage dibersihkan.');
        return redirect()->back()->with('success', 'Tembolok (Homepage Cache) berhasil dibersihkan!');
    }

    private function saveSettingKey(string $key, string $value): void
    {
        $db = Database::connect();
        if ($db->tableExists('settings')) {
            $check = $db->table('settings')->where('setting_key', $key)->get()->getRowArray();
            if ($check) {
                $db->table('settings')->where('setting_key', $key)->update(['setting_value' => $value]);
            } else {
                $data = ['setting_key' => $key, 'setting_value' => $value];
                if ($db->fieldExists('setting_group', 'settings')) {
                    $data['setting_group'] = 'homepage';
                }
                $db->table('settings')->insert($data);
            }
        }
    }

    private function purgeCache(): void
    {
        try {
            cache()->clean();
        } catch (\Throwable $e) {
            // Ignore if cache clean throws
        }
    }

    private function logActivity(string $title, string $description): void
    {
        $db = Database::connect();
        if ($db->tableExists('audit_logs')) {
            $data = [
                'action'      => $title,
                'module'      => 'homepage',
                'description' => $description,
                'ip_address'  => $this->request->getIPAddress(),
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            if ($db->fieldExists('user_id', 'audit_logs')) {
                $data['user_id'] = 1;
            }
            $db->table('audit_logs')->insert($data);
        } elseif ($db->tableExists('activity_logs')) {
            $db->table('activity_logs')->insert([
                'user_id'     => 1,
                'action'      => $title,
                'description' => $description,
                'ip_address'  => $this->request->getIPAddress(),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
