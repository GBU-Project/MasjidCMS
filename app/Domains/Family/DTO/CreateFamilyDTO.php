<?php

namespace App\Domains\Family\DTO;

/**
 * Class CreateFamilyDTO
 */
class CreateFamilyDTO
{
    public function __construct(
        public readonly string $family_no,
        public readonly string $name,
        public readonly ?string $kk_number = null,
        public readonly ?string $head_jamaah_id = null,
        public readonly ?string $address = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postal_code = null,
        public readonly string $family_status = 'ACTIVE',
        public readonly ?string $notes = null,
        public readonly ?string $created_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            family_no: trim($data['family_no'] ?? ''),
            name: trim($data['name'] ?? ''),
            kk_number: isset($data['kk_number']) ? trim($data['kk_number']) : null,
            head_jamaah_id: isset($data['head_jamaah_id']) ? trim($data['head_jamaah_id']) : null,
            address: isset($data['address']) ? trim($data['address']) : null,
            district: isset($data['district']) ? trim($data['district']) : null,
            city: isset($data['city']) ? trim($data['city']) : null,
            province: isset($data['province']) ? trim($data['province']) : null,
            postal_code: isset($data['postal_code']) ? trim($data['postal_code']) : null,
            family_status: strtoupper(trim($data['family_status'] ?? ($data['status'] ?? 'ACTIVE'))),
            notes: isset($data['notes']) ? trim($data['notes']) : null,
            created_by: isset($data['created_by']) ? (string)$data['created_by'] : null
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
            'created_by'     => $this->created_by,
        ], fn($value) => $value !== null);
    }
}
