<?php

namespace App\Domains\Masjid\Entities;

/**
 * Class Masjid
 *
 * Domain Entity representasi profil data Masjid.
 */
class Masjid
{
    public function __construct(
        public readonly int|string|null $id = null,
        public readonly string $code = '',
        public readonly string $name = '',
        public readonly string $slug = '',
        public readonly ?string $type = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $website = null,
        public readonly ?string $address = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $postal_code = null,
        public readonly float|string|null $latitude = null,
        public readonly float|string|null $longitude = null,
        public readonly ?string $timezone = 'Asia/Jakarta',
        public readonly int|string|null $logo_media_id = null,
        public readonly string $status = 'active',
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $deleted_at = null,
        public readonly int|string|null $created_by = null,
        public readonly int|string|null $updated_by = null,
        public readonly int|string|null $deleted_by = null
    ) {}

    public function isActive(): bool
    {
        return strtolower($this->status) === 'active';
    }

    public function isInactive(): bool
    {
        return strtolower($this->status) === 'inactive';
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'type'          => $this->type,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'website'       => $this->website,
            'address'       => $this->address,
            'district'      => $this->district,
            'city'          => $this->city,
            'province'      => $this->province,
            'postal_code'   => $this->postal_code,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'timezone'      => $this->timezone,
            'logo_media_id' => $this->logo_media_id,
            'status'        => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted_at'    => $this->deleted_at,
            'created_by'    => $this->created_by,
            'updated_by'    => $this->updated_by,
            'deleted_by'    => $this->deleted_by,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            code: $data['code'] ?? '',
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            type: $data['type'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            website: $data['website'] ?? null,
            address: $data['address'] ?? null,
            district: $data['district'] ?? null,
            city: $data['city'] ?? null,
            province: $data['province'] ?? null,
            postal_code: $data['postal_code'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            timezone: $data['timezone'] ?? 'Asia/Jakarta',
            logo_media_id: $data['logo_media_id'] ?? null,
            status: $data['status'] ?? 'active',
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null,
            created_by: $data['created_by'] ?? null,
            updated_by: $data['updated_by'] ?? null,
            deleted_by: $data['deleted_by'] ?? null
        );
    }
}
