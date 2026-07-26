<?php

namespace App\Domains\Masjid\DTO;

/**
 * Class CreateMasjidRequest
 *
 * Immutable Data Transfer Object untuk payload pembuatan profil Masjid baru.
 */
class CreateMasjidRequest
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $address = '',
        public readonly string $phone = '',
        public readonly string $email = '',
        public readonly string $website = '',
        public readonly string $status = 'active'
    ) {}

    /**
     * Factory pembuat DTO dari array input request.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            address: (string) ($data['address'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            website: (string) ($data['website'] ?? ''),
            status: (string) ($data['status'] ?? 'active')
        );
    }
}
