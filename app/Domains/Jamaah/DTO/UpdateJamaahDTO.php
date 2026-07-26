<?php

namespace App\Domains\Jamaah\DTO;

/**
 * Class UpdateJamaahDTO
 */
class UpdateJamaahDTO
{
    public function __construct(
        public readonly ?string $member_no = null,
        public readonly ?string $nik = null,
        public readonly ?string $full_name = null,
        public readonly ?string $gender = null,
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
        public readonly ?string $status = null,
        public readonly ?string $notes = null,
        public readonly ?string $updated_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            member_no: isset($data['member_no']) ? trim($data['member_no']) : null,
            nik: isset($data['nik']) ? trim($data['nik']) : null,
            full_name: isset($data['full_name']) ? trim($data['full_name']) : (isset($data['name']) ? trim($data['name']) : null),
            gender: isset($data['gender']) ? strtolower(trim($data['gender'])) : null,
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
            status: isset($data['status']) ? strtoupper(trim($data['status'])) : null,
            notes: isset($data['notes']) ? trim($data['notes']) : null,
            updated_by: isset($data['updated_by']) ? (string)$data['updated_by'] : null
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
            'updated_by'     => $this->updated_by,
        ], fn($value) => $value !== null);
    }
}
