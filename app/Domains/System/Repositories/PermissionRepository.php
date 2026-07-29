<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\System\Entities\Permission;

/**
 * Class PermissionRepository
 *
 * Repository skeleton untuk data Permission.
 */
class PermissionRepository extends BaseRepository
{
    protected string $table = 'permissions';

    /**
     * Mendapatkan daftar permission milik pengguna berdasarkan User ID.
     *
     * @param int|string $userId
     * @return array<Permission>
     */
    public function findByUserId(int|string $userId): array
    {
        if (empty($userId)) {
            return [];
        }

        // TASK-019A Security Blocker Remediation (29 Juli 2026):
        // Query sebelumnya join ke 'role_permissions.permission_id' yang
        // TIDAK ADA di skema (lihat migration CreateRbacTables -- kolom
        // sebenarnya adalah 'permission_code'), dan memetakan kolom
        // 'slug'/'module' yang juga tidak ada di tabel 'permissions'
        // (kolom sebenarnya 'permission_code'/'module_name'). Bug ini
        // baru terdeteksi sekarang karena filter 'rbac:<permission>'
        // sebelumnya tidak pernah benar-benar dieksekusi untuk user
        // non-Super-Admin (tidak ada rute yang memakainya).
        $rows = $this->builder()
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_code = permissions.permission_code')
            ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();

        $permissions = [];
        foreach ($rows as $row) {
            $permissions[] = new Permission(
                $row['id'] ?? null,
                $row['permission_code'] ?? '',
                $row['permission_code'] ?? '',
                $row['module_name'] ?? '',
                $row['description'] ?? ''
            );
        }

        return $permissions;
    }
}
