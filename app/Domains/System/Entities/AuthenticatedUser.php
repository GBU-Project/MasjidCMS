<?php

namespace App\Domains\System\Entities;

/**
 * Class AuthenticatedUser
 *
 * Entity representasi pengguna yang telah terotentikasi.
 */
class AuthenticatedUser
{
    public int|string|null $id = null;
    public string $username = '';
    public string $displayName = '';
    public string $email = '';
    public array $roles = [];
    public array $permissions = [];

    public function __construct(
        int|string|null $id = null,
        string $username = '',
        string $displayName = '',
        string $email = '',
        array $roles = [],
        array $permissions = []
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }
}
