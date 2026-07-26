<?php

namespace App\Domains\Jamaah\DTO;

/**
 * Class CreateJamaahDTO
 */
class CreateJamaahDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $status = 'active',
        public readonly int|string|null $created_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: trim($data['code'] ?? ''),
            name: trim($data['name'] ?? ''),
            slug: trim($data['slug'] ?? ''),
            status: $data['status'] ?? 'active',
            created_by: $data['created_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'code'       => $this->code,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'status'     => $this->status,
            'created_by' => $this->created_by,
        ], fn($value) => $value !== null);
    }
}
