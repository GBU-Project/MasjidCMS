<?php

namespace App\Core\Traits;

/**
 * Trait UuidTrait
 *
 * Skeleton trait untuk pembentukan Universally Unique Identifier (UUID).
 */
trait UuidTrait
{
    /**
     * Membentuk UUID v4 sederhana (Skeleton).
     *
     * @return string
     */
    protected function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
