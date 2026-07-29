<?php

namespace App\Domains\System\Entities;

/**
 * Class AuthenticatedUser
 *
 * Domain Entity representasi pengguna yang terotentikasi di dalam sistem.
 */
class AuthenticatedUser
{
    public int|string|null $id = null;
    public string $username = '';
    public string $displayName = '';
    public string $email = '';
    public ?string $passwordHash = null;
    public array $roles = [];
    public array $permissions = [];

    public function __construct(
        int|string|null $id = null,
        string $username = '',
        string $displayName = '',
        string $email = '',
        ?string $passwordHash = null,
        array $roles = [],
        array $permissions = []
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    /**
     * Memeriksa apakah user memiliki role tertentu.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true) || $this->isSuperAdmin();
    }

    /**
     * Memeriksa apakah user memiliki permission tertentu.
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true) || $this->isSuperAdmin();
    }

    /**
     * Memeriksa apakah user merupakan Super Admin.
     *
     * TASK-019A Hotfix (patch audit, 29 Juli 2026): sebelumnya hanya
     * mencocokkan literal 'superadmin'/'admin', padahal skema RBAC
     * sesungguhnya (RbacSeeder / CreateRbacTables) memakai role_code
     * 'SUPER_ADMIN'. Akibatnya method ini SELALU false untuk akun Super
     * Admin manapun -- filter 'rbac:xxx' menolak Super Admin dengan 403
     * karena RbacSeeder tidak pernah memberi role SUPER_ADMIN permission
     * eksplisit apa pun (didesain bergantung penuh pada bypass ini).
     * Perbandingan dibuat case-insensitive agar tetap kompatibel dengan
     * role_code lama ('superadmin'/'admin') bila ada di data existing.
     */
    public function isSuperAdmin(): bool
    {
        foreach ($this->roles as $role) {
            if (in_array(strtoupper((string) $role), ['SUPER_ADMIN', 'SUPERADMIN', 'ADMIN'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mendapatkan nama tampilan user (display name fallback ke username).
     */
    public function displayName(): string
    {
        return !empty($this->displayName) ? $this->displayName : $this->username;
    }
}
