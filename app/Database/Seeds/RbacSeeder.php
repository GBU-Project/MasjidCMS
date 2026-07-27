<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            [
                'id'          => 'r-super-admin-01',
                'role_code'   => 'SUPER_ADMIN',
                'name'        => 'Super Administrator',
                'description' => 'Akses penuh platform lintas masjid',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 'r-admin-masjid-02',
                'role_code'   => 'ADMIN_MASJID',
                'name'        => 'Admin Masjid',
                'description' => 'Pengelola utama tingkat masjid',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 'r-operator-03',
                'role_code'   => 'OPERATOR',
                'name'        => 'Operator Takmir',
                'description' => 'Petugas entri data operasional harian',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'id'          => 'r-viewer-04',
                'role_code'   => 'VIEWER',
                'name'        => 'Viewer / Publik',
                'description' => 'Akses baca umum data masjid',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $rolesTable = $this->db->table('roles');
        foreach ($roles as $role) {
            $existing = $rolesTable->where('role_code', $role['role_code'])->get()->getRow();
            if (!$existing) {
                $rolesTable->insert($role);
            }
        }

        // 2. Seed Permissions
        $permissions = [
            ['id' => 'p-01', 'permission_code' => 'dashboard.view', 'module_name' => 'System', 'description' => 'Melihat dashboard utama'],
            ['id' => 'p-02', 'permission_code' => 'jamaah.read', 'module_name' => 'Jamaah', 'description' => 'Melihat data jamaah'],
            ['id' => 'p-03', 'permission_code' => 'jamaah.create', 'module_name' => 'Jamaah', 'description' => 'Membuat data jamaah'],
            ['id' => 'p-04', 'permission_code' => 'jamaah.update', 'module_name' => 'Jamaah', 'description' => 'Mengubah data jamaah'],
            ['id' => 'p-05', 'permission_code' => 'jamaah.delete', 'module_name' => 'Jamaah', 'description' => 'Menghapus data jamaah'],
            ['id' => 'p-06', 'permission_code' => 'family.read', 'module_name' => 'Family', 'description' => 'Melihat data keluarga'],
            ['id' => 'p-07', 'permission_code' => 'family.create', 'module_name' => 'Family', 'description' => 'Membuat data keluarga'],
            ['id' => 'p-08', 'permission_code' => 'family.update', 'module_name' => 'Family', 'description' => 'Mengubah data keluarga'],
            ['id' => 'p-09', 'permission_code' => 'family.delete', 'module_name' => 'Family', 'description' => 'Menghapus data keluarga'],
            ['id' => 'p-10', 'permission_code' => 'admin.manage', 'module_name' => 'Admin', 'description' => 'Manajemen pengguna dan peran'],
        ];

        $permTable = $this->db->table('permissions');
        foreach ($permissions as $perm) {
            $existing = $permTable->where('permission_code', $perm['permission_code'])->get()->getRow();
            if (!$existing) {
                $perm['created_at'] = date('Y-m-d H:i:s');
                $permTable->insert($perm);
            }
        }

        // 3. Seed Role Permissions Mapping
        $rolePerms = [
            // Admin Masjid permissions
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'dashboard.view'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'jamaah.read'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'jamaah.create'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'jamaah.update'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'jamaah.delete'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'family.read'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'family.create'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'family.update'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'family.delete'],
            ['role_id' => 'r-admin-masjid-02', 'permission_code' => 'admin.manage'],

            // Operator permissions
            ['role_id' => 'r-operator-03', 'permission_code' => 'dashboard.view'],
            ['role_id' => 'r-operator-03', 'permission_code' => 'jamaah.read'],
            ['role_id' => 'r-operator-03', 'permission_code' => 'jamaah.create'],
            ['role_id' => 'r-operator-03', 'permission_code' => 'jamaah.update'],
            ['role_id' => 'r-operator-03', 'permission_code' => 'family.read'],

            // Viewer permissions
            ['role_id' => 'r-viewer-04', 'permission_code' => 'dashboard.view'],
            ['role_id' => 'r-viewer-04', 'permission_code' => 'jamaah.read'],
            ['role_id' => 'r-viewer-04', 'permission_code' => 'family.read'],
        ];

        $rpTable = $this->db->table('role_permissions');
        foreach ($rolePerms as $rp) {
            $existing = $rpTable->where('role_id', $rp['role_id'])->where('permission_code', $rp['permission_code'])->get()->getRow();
            if (!$existing) {
                $rpTable->insert($rp);
            }
        }
    }
}
