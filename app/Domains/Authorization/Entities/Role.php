<?php

namespace App\Domains\Authorization\Entities;

/**
 * Class Role
 *
 * Domain Entity representasi Peran (Role) RBAC.
 */
class Role
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly string $role_code = '',
        public readonly string $name = '',
        public readonly ?string $description = null,
        public readonly array $permissions = [],
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'role_code'   => $this->role_code,
            'name'        => $this->name,
            'description' => $this->description,
            'permissions' => $this->permissions,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            role_code: $data['role_code'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            permissions: $data['permissions'] ?? [],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }
}
