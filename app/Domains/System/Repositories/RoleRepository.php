<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\System\Entities\Role;

/**
 * Class RoleRepository
 *
 * Repository skeleton untuk data Role.
 */
class RoleRepository extends BaseRepository
{
    protected string $table = 'roles';

    /**
     * Mendapatkan daftar role milik pengguna.
     *
     * @param int|string $userId
     * @return array<Role>
     */
    public function findByUserId(int|string $userId): array
    {
        if (empty($userId)) {
            return [];
        }

        // TASK-019A Security Blocker Remediation (29 Juli 2026):
        // Kolom 'slug' sebelumnya tidak ada di skema tabel 'roles'
        // (kolom sebenarnya adalah 'role_code' -- lihat migration
        // CreateRbacTables). Baru terdeteksi sekarang karena jalur ini
        // sebelumnya tidak pernah benar-benar dipakai untuk memutuskan
        // izin akses non-Super-Admin.
        $rows = $this->builder()
            ->select('roles.*')
            ->join('user_roles', 'user_roles.role_id = roles.id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();

        $roles = [];
        foreach ($rows as $row) {
            $roles[] = new Role(
                $row['id'] ?? null,
                $row['name'] ?? '',
                $row['role_code'] ?? '',
                $row['description'] ?? ''
            );
        }

        return $roles;
    }
}
