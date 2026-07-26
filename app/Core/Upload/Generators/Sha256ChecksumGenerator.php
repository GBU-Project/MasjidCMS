<?php

namespace App\Core\Upload\Generators;

use App\Core\Contracts\Upload\ChecksumGeneratorInterface;

/**
 * Class Sha256ChecksumGenerator
 *
 * Perhitungan checksum berbasis algoritma SHA-256.
 */
class Sha256ChecksumGenerator implements ChecksumGeneratorInterface
{
    public function generate(string $contentsOrPath): string
    {
        if (file_exists($contentsOrPath) && is_file($contentsOrPath)) {
            return hash_file('sha256', $contentsOrPath);
        }

        return hash('sha256', $contentsOrPath);
    }
}
