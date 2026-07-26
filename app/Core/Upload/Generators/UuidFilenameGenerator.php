<?php

namespace App\Core\Upload\Generators;

use App\Core\Contracts\Upload\FilenameGeneratorInterface;
use App\Core\Upload\UploadContext;

/**
 * Class UuidFilenameGenerator
 *
 * Strategi penamaan berkas berbasis UUID v4 acak.
 */
class UuidFilenameGenerator implements FilenameGeneratorInterface
{
    public function generate(UploadContext $context): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        $ext = !empty($context->extension) ? '.' . ltrim($context->extension, '.') : '';

        return $uuid . $ext;
    }
}
