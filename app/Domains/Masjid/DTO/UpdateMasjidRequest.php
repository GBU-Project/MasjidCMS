<?php

namespace App\Domains\Masjid\DTO;

/**
 * Class UpdateMasjidRequest
 *
 * Immutable Data Transfer Object untuk payload pembaruan profil Masjid.
 */
class UpdateMasjidRequest
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $slug = null,
        public readonly ?string $address = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
        public readonly ?string $status = null
    ) {}

    /**
     * Factory pembuat DTO dari array input request.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) ? (string) $data['name'] : null,
            slug: isset($data['slug']) ? (string) $data['slug'] : null,
            address: isset($data['address']) ? (string) $data['address'] : null,
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            website: isset($data['website']) ? (string) $data['website'] : null,
            status: isset($data['status']) ? (string) $data['status'] : null
        );
    }
}
