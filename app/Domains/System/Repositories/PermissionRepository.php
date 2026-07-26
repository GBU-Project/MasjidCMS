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

        // Skeleton query builder placeholder
        $rows = $this->builder()
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();

        $permissions = [];
        foreach ($rows as $row) {
            $permissions[] = new Permission(
                $row['id'] ?? null,
                $row['name'] ?? '',
                $row['slug'] ?? '',
                $row['module'] ?? '',
                $row['description'] ?? ''
            );
        }

        return $permissions;
    }
}
