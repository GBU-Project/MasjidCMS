<?php

namespace App\Domains\Masjid\DTO;

/**
 * Class CreateMasjidDTO
 */
class CreateMasjidDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $slug,
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
        public readonly int|string|null $created_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: trim($data['code'] ?? ''),
            name: trim($data['name'] ?? ''),
            slug: trim($data['slug'] ?? ''),
            type: isset($data['type']) ? trim($data['type']) : null,
            email: isset($data['email']) ? trim($data['email']) : null,
            phone: isset($data['phone']) ? trim($data['phone']) : null,
            website: isset($data['website']) ? trim($data['website']) : null,
            address: isset($data['address']) ? trim($data['address']) : null,
            district: isset($data['district']) ? trim($data['district']) : null,
            city: isset($data['city']) ? trim($data['city']) : null,
            province: isset($data['province']) ? trim($data['province']) : null,
            postal_code: isset($data['postal_code']) ? trim($data['postal_code']) : null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            timezone: $data['timezone'] ?? 'Asia/Jakarta',
            logo_media_id: $data['logo_media_id'] ?? null,
            status: $data['status'] ?? 'active',
            created_by: $data['created_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
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
            'created_by'    => $this->created_by,
        ], fn($value) => $value !== null);
    }
}
