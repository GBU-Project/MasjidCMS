<?php

namespace App\Domains\Jamaah\DTO;

/**
 * Class CreateJamaahDTO
 */
class CreateJamaahDTO
{
    public function __construct(
        public readonly string $member_no,
        public readonly string $nik,
        public readonly string $full_name,
        public readonly string $gender = 'male',
        public readonly ?string $birth_place = null,
        public readonly ?string $birth_date = null,
        public readonly ?string $address = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postal_code = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $occupation = null,
        public readonly ?string $education = null,
        public readonly ?string $marital_status = null,
        public readonly ?string $family_id = null,
        public readonly string $status = 'ACTIVE',
        public readonly ?string $notes = null,
        public readonly ?string $created_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            member_no: trim($data['member_no'] ?? ''),
            nik: trim($data['nik'] ?? ''),
            full_name: trim($data['full_name'] ?? ($data['name'] ?? '')),
            gender: strtolower(trim($data['gender'] ?? 'male')),
            birth_place: isset($data['birth_place']) ? trim($data['birth_place']) : null,
            birth_date: isset($data['birth_date']) ? trim($data['birth_date']) : null,
            address: isset($data['address']) ? trim($data['address']) : null,
            district: isset($data['district']) ? trim($data['district']) : null,
            city: isset($data['city']) ? trim($data['city']) : null,
            province: isset($data['province']) ? trim($data['province']) : null,
            postal_code: isset($data['postal_code']) ? trim($data['postal_code']) : null,
            phone: isset($data['phone']) ? trim($data['phone']) : null,
            email: isset($data['email']) ? trim($data['email']) : null,
            occupation: isset($data['occupation']) ? trim($data['occupation']) : null,
            education: isset($data['education']) ? trim($data['education']) : null,
            marital_status: isset($data['marital_status']) ? trim($data['marital_status']) : null,
            family_id: isset($data['family_id']) ? trim($data['family_id']) : null,
            status: strtoupper(trim($data['status'] ?? 'ACTIVE')),
            notes: isset($data['notes']) ? trim($data['notes']) : null,
            created_by: isset($data['created_by']) ? (string)$data['created_by'] : null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'member_no'      => $this->member_no,
            'nik'            => $this->nik,
            'full_name'      => $this->full_name,
            'gender'         => $this->gender,
            'birth_place'    => $this->birth_place,
            'birth_date'     => $this->birth_date,
            'address'        => $this->address,
            'district'       => $this->district,
            'city'           => $this->city,
            'province'       => $this->province,
            'postal_code'    => $this->postal_code,
            'phone'          => $this->phone,
            'email'          => $this->email,
            'occupation'     => $this->occupation,
            'education'      => $this->education,
            'marital_status' => $this->marital_status,
            'family_id'      => $this->family_id,
            'status'         => $this->status,
            'notes'          => $this->notes,
            'created_by'     => $this->created_by,
        ], fn($value) => $value !== null);
    }
}
