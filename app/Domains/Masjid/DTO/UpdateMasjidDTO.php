<?php

namespace App\Domains\Masjid\DTO;

/**
 * Class UpdateMasjidDTO
 */
class UpdateMasjidDTO
{
    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $name = null,
        public readonly ?string $slug = null,
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
        public readonly ?string $timezone = null,
        public readonly int|string|null $logo_media_id = null,
        public readonly ?string $status = null,
        public readonly int|string|null $updated_by = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: isset($data['code']) ? trim($data['code']) : null,
            name: isset($data['name']) ? trim($data['name']) : null,
            slug: isset($data['slug']) ? trim($data['slug']) : null,
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
            timezone: $data['timezone'] ?? null,
            logo_media_id: $data['logo_media_id'] ?? null,
            status: $data['status'] ?? null,
            updated_by: $data['updated_by'] ?? null
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
            'updated_by'    => $this->updated_by,
        ], fn($value) => $value !== null);
    }
}
