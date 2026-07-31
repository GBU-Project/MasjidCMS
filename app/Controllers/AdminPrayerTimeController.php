<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminPrayerTimeController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $db = Database::connect();
        $prayerTimes = [];
        $settings = [];

        // Fetch settings
        if ($db->tableExists('settings')) {
            $raw = $db->table('settings')->get()->getResultArray();
            foreach ($raw as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        // Fetch prayer times
        if ($db->tableExists('prayer_times')) {
            $prayerTimes = $db->table('prayer_times')
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();
        }

        return view('admin/prayer/index', [
            'activePage'  => 'prayer-time',
            'prayerTimes' => $prayerTimes,
            'settings'    => $settings,
        ]);
    }

    public function update()
    {
        $db = Database::connect();
        $postData = $this->request->getPost();

        if (!$db->tableExists('prayer_times')) {
            return redirect()->back()->with('error', 'Tabel jadwal sholat belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        // Update individual prayer times
        $prayerNames = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
        foreach ($prayerNames as $name) {
            $timeKey = 'prayer_time_' . strtolower($name);
            $iqamahKey = 'iqamah_time_' . strtolower($name);
            $activeKey = 'is_active_' . strtolower($name);

            if (isset($postData[$timeKey])) {
                $updateData = [
                    'prayer_time' => $postData[$timeKey],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
                if (isset($postData[$iqamahKey])) {
                    $updateData['iqamah_time'] = $postData[$iqamahKey] ?: null;
                }
                if (isset($postData[$activeKey])) {
                    $updateData['is_active'] = (int) $postData[$activeKey];
                }
                $db->table('prayer_times')
                    ->where('prayer_name', $name)
                    ->update($updateData);
            }
        }

        // Update prayer settings
        $settingKeys = ['prayer_city', 'prayer_method', 'prayer_auto_update', 'prayer_latitude', 'prayer_longitude', 'prayer_timezone'];
        foreach ($settingKeys as $key) {
            if (isset($postData[$key])) {
                $this->saveSettingKey($key, (string) $postData[$key]);
            }
        }

        $this->logActivity('Prayer times updated', 'Pengurus memperbarui jadwal sholat dan konfigurasi.');
        $this->purgeCache();

        return redirect()->back()->with('success', 'Jadwal Sholat berhasil diperbarui!');
    }

    public function resetDefault()
    {
        $db = Database::connect();
        if (!$db->tableExists('prayer_times')) {
            return redirect()->back()->with('error', 'Tabel jadwal sholat belum tersedia.');
        }

        $defaultTimes = [
            ['prayer_name' => 'Subuh',   'prayer_time' => '04:38:00', 'iqamah_time' => '04:58:00', 'is_active' => 1, 'sort_order' => 1],
            ['prayer_name' => 'Dzuhur',  'prayer_time' => '12:05:00', 'iqamah_time' => '12:25:00', 'is_active' => 1, 'sort_order' => 2],
            ['prayer_name' => 'Ashar',   'prayer_time' => '15:20:00', 'iqamah_time' => '15:40:00', 'is_active' => 1, 'sort_order' => 3],
            ['prayer_name' => 'Maghrib', 'prayer_time' => '18:02:00', 'iqamah_time' => '18:12:00', 'is_active' => 1, 'sort_order' => 4],
            ['prayer_name' => 'Isya',    'prayer_time' => '19:14:00', 'iqamah_time' => '19:34:00', 'is_active' => 1, 'sort_order' => 5],
        ];

        foreach ($defaultTimes as $pt) {
            $db->table('prayer_times')
                ->where('prayer_name', $pt['prayer_name'])
                ->update([
                    'prayer_time' => $pt['prayer_time'],
                    'iqamah_time' => $pt['iqamah_time'],
                    'is_active'   => $pt['is_active'],
                    'sort_order'  => $pt['sort_order'],
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
        }

        $this->logActivity('Prayer times reset', 'Jadwal sholat dikembalikan ke nilai standar.');
        $this->purgeCache();

        return redirect()->back()->with('success', 'Jadwal Sholat berhasil di-reset ke default!');
    }

    // ---- API: Get prayer times for frontend ----
    public function apiGetTimes()
    {
        $db = Database::connect();
        $prayerTimes = [];
        $settings = [];

        if ($db->tableExists('prayer_times')) {
            $prayerTimes = $db->table('prayer_times')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()
                ->getResultArray();
        }

        if ($db->tableExists('settings')) {
            $raw = $db->table('settings')->where('setting_group', 'prayer')->get()->getResultArray();
            foreach ($raw as $s) {
                $settings[$s['setting_key']] = $s['setting_value'];
            }
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'data'    => $prayerTimes,
            'settings' => $settings,
        ]);
    }

    // ---- Helper Methods ----
    private function saveSettingKey(string $key, string $value): void
    {
        $db = Database::connect();
        if ($db->tableExists('settings')) {
            $check = $db->table('settings')->where('setting_key', $key)->get()->getRowArray();
            if ($check) {
                $db->table('settings')->where('setting_key', $key)->update(['setting_value' => $value]);
            } else {
                $data = ['setting_key' => $key, 'setting_value' => $value, 'setting_group' => 'prayer'];
                $db->table('settings')->insert($data);
            }
        }
    }

    private function purgeCache(): void
    {
        try {
            cache()->clean();
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    private function logActivity(string $title, string $description): void
    {
        $db = Database::connect();
        if ($db->tableExists('audit_logs')) {
            $data = [
                'action'      => $title,
                'module'      => 'prayer',
                'description' => $description,
                'ip_address'  => $this->request->getIPAddress(),
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            if ($db->fieldExists('user_id', 'audit_logs')) {
                $data['user_id'] = session()->get('auth_user')['id'] ?? 1;
            }
            $db->table('audit_logs')->insert($data);
        } elseif ($db->tableExists('activity_logs')) {
            $db->table('activity_logs')->insert([
                'user_id'     => session()->get('auth_user')['id'] ?? 1,
                'action'      => $title,
                'description' => $description,
                'ip_address'  => $this->request->getIPAddress(),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }
}