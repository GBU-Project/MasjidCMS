<?php

namespace App\Domains\Authorization\Entities;

/**
 * Class Permission
 *
 * Domain Entity representasi Hak Akses (Permission) RBAC.
 */
class Permission
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly string $permission_code = '',
        public readonly string $module_name = '',
        public readonly ?string $description = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null
    ) {}

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'permission_code' => $this->permission_code,
            'module_name'     => $this->module_name,
            'description'     => $this->description,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            permission_code: $data['permission_code'] ?? '',
            module_name: $data['module_name'] ?? '',
            description: $data['description'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null
        );
    }
}
