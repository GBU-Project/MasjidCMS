<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use Config\Database;

class AdminSystemWorkspaceController extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getGet('tab') ?? 'settings');

        $headers = [];
        $rows = [];

        if ($tab === 'settings' && $db->tableExists('settings')) {
            $headers = ['Setting Key', 'Setting Value', 'Grup Konfigurasi'];
            $data = $db->table('settings')->get()->getResultArray();
            foreach ($data as $s) {
                $rows[] = [
                    'columns' => [
                        '<span class="stat-mono">' . esc($s['setting_key']) . '</span>',
                        '<strong>' . esc($s['setting_value']) . '</strong>',
                        '<span class="badge badge-green">' . esc($s['setting_group']) . '</span>',
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

        $moduleLabels = [
            'settings' => 'Pengaturan Platform System',
            'audit'    => 'Audit Activity Log',
        ];

        return view('admin/system/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Pengaturan Platform System',
            'headers'           => $headers,
            'rows'              => $rows,
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
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminSystemWorkspaceController Store Exception: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/settings?tab=settings'));
    }
}
