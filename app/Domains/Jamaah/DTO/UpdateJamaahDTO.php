<?php

namespace App\Domains\Jamaah\DTO;

/**
 * Class UpdateJamaahDTO
 */
class UpdateJamaahDTO
{
    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $name = null,
        public readonly ?string $slug = null,
        public readonly ?string $status = null,
        public readonly int|string|null $updated_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: isset($data['code']) ? trim($data['code']) : null,
            name: isset($data['name']) ? trim($data['name']) : null,
            slug: isset($data['slug']) ? trim($data['slug']) : null,
            status: $data['status'] ?? null,
            updated_by: $data['updated_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'code'       => $this->code,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'status'     => $this->status,
            'updated_by' => $this->updated_by,
        ], fn($value) => $value !== null);
    }
}
