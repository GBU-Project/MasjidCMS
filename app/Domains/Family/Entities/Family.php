<?php

namespace App\Domains\Family\Entities;

/**
 * Class Family
 *
 * Domain Entity representasi data Keluarga (Kartu Keluarga).
 */
class Family
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly string $family_no = '',
        public readonly ?string $kk_number = null,
        public readonly string $name = '',
        public readonly ?string $head_jamaah_id = null,
        public readonly ?string $address = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postal_code = null,
        public readonly string $family_status = 'ACTIVE',
        public readonly ?string $notes = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $deleted_at = null,
        public readonly ?string $created_by = null,
        public readonly ?string $updated_by = null,
        public readonly ?string $deleted_by = null
    ) {}

    public function isActive(): bool
    {
        return strtoupper($this->family_status) === 'ACTIVE';
    }

    public function isInactive(): bool
    {
        return strtoupper($this->family_status) === 'INACTIVE';
    }

    public function isMoved(): bool
    {
        return strtoupper($this->family_status) === 'MOVED';
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'family_no'      => $this->family_no,
            'kk_number'      => $this->kk_number,
            'name'           => $this->name,
            'head_jamaah_id' => $this->head_jamaah_id,
            'address'        => $this->address,
            'district'       => $this->district,
            'city'           => $this->city,
            'province'       => $this->province,
            'postal_code'    => $this->postal_code,
            'family_status'  => $this->family_status,
            'notes'          => $this->notes,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'deleted_at'     => $this->deleted_at,
            'created_by'     => $this->created_by,
            'updated_by'     => $this->updated_by,
            'deleted_by'     => $this->deleted_by,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            family_no: $data['family_no'] ?? '',
            kk_number: $data['kk_number'] ?? null,
            name: $data['name'] ?? '',
            head_jamaah_id: $data['head_jamaah_id'] ?? null,
            address: $data['address'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            postal_code: $data['postal_code'] ?? null,
            family_status: $data['family_status'] ?? ($data['status'] ?? 'ACTIVE'),
            notes: $data['notes'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null,
            created_by: $data['created_by'] ?? null,
            updated_by: $data['updated_by'] ?? null,
            deleted_by: $data['deleted_by'] ?? null
        );
    }
}
