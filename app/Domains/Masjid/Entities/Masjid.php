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
        public readonly string $address = '',
        public readonly string $phone = '',
        public readonly string $email = '',
        public readonly string $website = '',
        public readonly string $status = 'active',
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null
    ) {}
}
