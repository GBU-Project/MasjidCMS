<?php

namespace App\Core\Contracts\Auth;

use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Interface PermissionProviderInterface
 *
 * Kontrak standar untuk penyedia data hak akses (Role & Permission) di MasjidCMS.
 * Mengisolasi RBAC Service dari sumber penyimpanan permission (Database, Config, External Policy).
 */
interface PermissionProviderInterface
{
    /**
     * Mendapatkan daftar role yang dimiliki oleh pengguna.
     *
     * @param AuthenticatedUser $user
     * @return array<string>
     */
    public function getRoles(AuthenticatedUser $user): array;

    /**
     * Mendapatkan daftar permission yang dimiliki oleh pengguna.
     *
     * @param AuthenticatedUser $user
     * @return array<string>
     */
    public function getPermissions(AuthenticatedUser $user): array;

    /**
     * Memeriksa apakah pengguna memiliki role tertentu.
     *
     * @param AuthenticatedUser $user
     * @param string $role
     * @return bool
     */
    public function hasRole(AuthenticatedUser $user, string $role): bool;

    /**
     * Memeriksa apakah pengguna memiliki permission tertentu.
     *
     * @param AuthenticatedUser $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(AuthenticatedUser $user, string $permission): bool;
}
