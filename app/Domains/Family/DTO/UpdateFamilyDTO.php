<?php

namespace App\Domains\Family\DTO;

/**
 * Class UpdateFamilyDTO
 */
class UpdateFamilyDTO
{
    public function __construct(
        public readonly ?string $family_no = null,
        public readonly ?string $name = null,
        public readonly ?string $kk_number = null,
        public readonly ?string $head_jamaah_id = null,
        public readonly ?string $address = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postal_code = null,
        public readonly ?string $family_status = null,
        public readonly ?string $notes = null,
        public readonly ?string $updated_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            family_no: isset($data['family_no']) ? trim($data['family_no']) : null,
            name: isset($data['name']) ? trim($data['name']) : null,
            kk_number: isset($data['kk_number']) ? trim($data['kk_number']) : null,
            head_jamaah_id: isset($data['head_jamaah_id']) ? trim($data['head_jamaah_id']) : null,
            address: isset($data['address']) ? trim($data['address']) : null,
            district: isset($data['district']) ? trim($data['district']) : null,
            city: isset($data['city']) ? trim($data['city']) : null,
            province: isset($data['province']) ? trim($data['province']) : null,
            postal_code: isset($data['postal_code']) ? trim($data['postal_code']) : null,
            family_status: isset($data['family_status']) ? strtoupper(trim($data['family_status'])) : (isset($data['status']) ? strtoupper(trim($data['status'])) : null),
            notes: isset($data['notes']) ? trim($data['notes']) : null,
            updated_by: isset($data['updated_by']) ? (string)$data['updated_by'] : null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'family_no'      => $this->family_no,
            'name'           => $this->name,
            'kk_number'      => $this->kk_number,
            'head_jamaah_id' => $this->head_jamaah_id,
            'address'        => $this->address,
            'district'       => $this->district,
            'city'           => $this->city,
            'province'       => $this->province,
            'postal_code'    => $this->postal_code,
            'family_status'  => $this->family_status,
            'notes'          => $this->notes,
            'updated_by'     => $this->updated_by,
        ], fn($value) => $value !== null);
    }
}
