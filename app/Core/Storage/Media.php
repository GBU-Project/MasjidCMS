<?php

namespace App\Core\Storage;

/**
 * Class Media
 *
 * Domain Entity representasi berkas / media storage.
 */
class Media
{
    public function __construct(
        public readonly int|string|null $id = null,
        public readonly string $disk = 'local',
        public readonly string $path = '',
        public readonly string $filename = '',
        public readonly string $extension = '',
        public readonly string $mimeType = 'application/octet-stream',
        public readonly int $size = 0,
        public readonly string $checksum = '',
        public readonly string $visibility = 'public',
        public readonly ?string $createdAt = null
    ) {}
}
