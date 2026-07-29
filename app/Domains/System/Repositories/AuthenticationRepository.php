<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Class AuthenticationRepository
 *
 * Database Access Layer khusus penyedia identitas berbasis database local.
 */
class AuthenticationRepository extends BaseRepository
{
    protected string $table = 'users';

    protected RoleRepository $roleRepository;
    protected PermissionRepository $permissionRepository;

    public function __construct(
        ?RoleRepository $roleRepository = null,
        ?PermissionRepository $permissionRepository = null
    ) {
        parent::__construct();
        $this->roleRepository = $roleRepository ?? new RoleRepository();
        $this->permissionRepository = $permissionRepository ?? new PermissionRepository();
    }

    /**
     * Mencari data pengguna untuk otentikasi berdasarkan username atau email.
     *
     * @param string $identifier
     * @return AuthenticatedUser|null
     */
    public function findForAuthentication(string $identifier): ?AuthenticatedUser
    {
        if (empty(trim($identifier))) {
            return null;
        }

        $row = $this->builder()
            ->groupStart()
                ->where('username', $identifier)
                ->orWhere('email', $identifier)
            ->groupEnd()
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari pengguna berdasarkan Primary ID.
     *
     * @param int|string $id
     * @return AuthenticatedUser|null
     */
    public function findById(int|string $id): ?AuthenticatedUser
    {
        if (empty($id)) {
            return null;
        }

        $row = $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Data mapper dari raw database row ke AuthenticatedUser Entity.
     *
     * @param array|null $data
     * @return AuthenticatedUser|null
     */
    protected function mapToEntity(?array $data): ?AuthenticatedUser
    {
        if (empty($data)) {
            return null;
        }

        $userId = $data['id'] ?? null;

        // TASK-019A Hotfix: 'users' table tidak memiliki kolom 'roles'/
        // 'permissions' (lihat migration CreateRbacTables) -- data tsb
        // sebelumnya SELALU kosong karena dibaca dari kolom yang tidak
        // ada. Roles/permissions sesungguhnya berasal dari tabel
        // roles/permissions/user_roles/role_permissions via repository
        // yang sama dipakai DatabasePermissionProvider, supaya
        // AuthenticatedUser::isSuperAdmin() dan cache di sesi konsisten
        // dengan pemeriksaan permission granular saat request berikutnya.
        $roleCodes = [];
        $permissionCodes = [];

        if ($userId !== null) {
            $roleCodes = array_map(
                static fn ($role) => $role->slug,
                $this->roleRepository->findByUserId($userId)
            );
            $permissionCodes = array_map(
                static fn ($permission) => $permission->slug,
                $this->permissionRepository->findByUserId($userId)
            );
        }

        return new AuthenticatedUser(
            id: $userId,
            username: $data['username'] ?? '',
            displayName: $data['display_name'] ?? $data['username'] ?? '',
            email: $data['email'] ?? '',
            passwordHash: $data['password_hash'] ?? $data['password'] ?? null,
            roles: $roleCodes,
            permissions: $permissionCodes
        );
    }
}
