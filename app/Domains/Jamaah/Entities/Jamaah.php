<?php

namespace App\Domains\Jamaah\Entities;

/**
 * Class Jamaah
 *
 * Domain Entity representasi data Jamaah Masjid.
 */
class Jamaah
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly string $member_no = '',
        public readonly string $nik = '',
        public readonly string $full_name = '',
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
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $deleted_at = null,
        public readonly ?string $created_by = null,
        public readonly ?string $updated_by = null,
        public readonly ?string $deleted_by = null
    ) {}

    public function isActive(): bool
    {
        return strtoupper($this->status) === 'ACTIVE';
    }

    public function isInactive(): bool
    {
        return strtoupper($this->status) === 'INACTIVE';
    }

    public function isMoved(): bool
    {
        return strtoupper($this->status) === 'MOVED';
    }

    public function isDeceased(): bool
    {
        return strtoupper($this->status) === 'DECEASED';
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
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
            member_no: $data['member_no'] ?? '',
            nik: $data['nik'] ?? '',
            full_name: $data['full_name'] ?? ($data['name'] ?? ''),
            gender: $data['gender'] ?? 'male',
            birth_place: $data['birth_place'] ?? null,
            birth_date: $data['birth_date'] ?? null,
            address: $data['address'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            postal_code: $data['postal_code'] ?? null,
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
            occupation: $data['occupation'] ?? null,
            education: $data['education'] ?? null,
            marital_status: $data['marital_status'] ?? null,
            family_id: $data['family_id'] ?? null,
            status: $data['status'] ?? 'ACTIVE',
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
