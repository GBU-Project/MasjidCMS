<?php

namespace App\Domains\Authorization\Entities;

/**
 * Class User
 *
 * Domain Entity representasi Pengguna Sistem RBAC.
 */
class User
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly string $username = '',
        public readonly string $email = '',
        public readonly string $password_hash = '',
        public readonly ?string $jamaah_id = null,
        public readonly string $status = 'ACTIVE',
        public readonly array $roles = [],
        public readonly array $permissions = [],
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $deleted_at = null
    ) {}

    public function isActive(): bool
    {
        return strtoupper($this->status) === 'ACTIVE';
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'jamaah_id'     => $this->jamaah_id,
            'status'        => $this->status,
            'roles'         => $this->roles,
            'permissions'   => $this->permissions,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted_at'    => $this->deleted_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            username: $data['username'] ?? '',
            email: $data['email'] ?? '',
            password_hash: $data['password_hash'] ?? '',
            jamaah_id: $data['jamaah_id'] ?? null,
            status: $data['status'] ?? 'ACTIVE',
            roles: $data['roles'] ?? [],
            permissions: $data['permissions'] ?? [],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }
}
