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
     */
    public function isSuperAdmin(): bool
    {
        return in_array('superadmin', $this->roles, true) || in_array('admin', $this->roles, true);
    }

    /**
     * Mendapatkan nama tampilan user (display name fallback ke username).
     */
    public function displayName(): string
    {
        return !empty($this->displayName) ? $this->displayName : $this->username;
    }
}
