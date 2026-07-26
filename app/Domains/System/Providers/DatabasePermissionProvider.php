<?php

namespace App\Domains\System\Providers;

use App\Core\Contracts\Auth\PermissionProviderInterface;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Repositories\PermissionRepository;
use App\Domains\System\Repositories\RoleRepository;

/**
 * Class DatabasePermissionProvider
 *
 * Implementasi Provider Hak Akses (RBAC) berbasis Database lokal.
 */
class DatabasePermissionProvider implements PermissionProviderInterface
{
    protected RoleRepository $roleRepository;
    protected PermissionRepository $permissionRepository;

    public function __construct(
        ?RoleRepository $roleRepository = null,
        ?PermissionRepository $permissionRepository = null
    ) {
        $this->roleRepository = $roleRepository ?? new RoleRepository();
        $this->permissionRepository = $permissionRepository ?? new PermissionRepository();
    }

    /**
     * Mendapatkan daftar role slug milik pengguna.
     */
    public function getRoles(AuthenticatedUser $user): array
    {
        if (!empty($user->roles)) {
            return $user->roles;
        }

        if ($user->id === null) {
            return [];
        }

        $roles = $this->roleRepository->findByUserId($user->id);
        return array_map(fn($role) => $role->slug, $roles);
    }

    /**
     * Mendapatkan daftar permission slug milik pengguna.
     */
    public function getPermissions(AuthenticatedUser $user): array
    {
        if (!empty($user->permissions)) {
            return $user->permissions;
        }

        if ($user->id === null) {
            return [];
        }

        $permissions = $this->permissionRepository->findByUserId($user->id);
        return array_map(fn($permission) => $permission->slug, $permissions);
    }

    /**
     * Memeriksa apakah pengguna memiliki role tertentu.
     */
    public function hasRole(AuthenticatedUser $user, string $role): bool
    {
        $userRoles = $this->getRoles($user);
        return in_array($role, $userRoles, true) || $user->isSuperAdmin();
    }

    /**
     * Memeriksa apakah pengguna memiliki permission tertentu.
     */
    public function hasPermission(AuthenticatedUser $user, string $permission): bool
    {
        $userPermissions = $this->getPermissions($user);
        return in_array($permission, $userPermissions, true) || $user->isSuperAdmin();
    }
}
