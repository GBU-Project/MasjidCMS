<?php

namespace App\Core\Contracts\Upload;

use App\Core\Upload\UploadContext;

/**
 * Interface FilenameGeneratorInterface
 *
 * Kontrak strategi penamaan berkas hasil upload.
 */
interface FilenameGeneratorInterface
{
    /**
     * Membentuk nama unik berkas baru.
     *
     * @param UploadContext $context
     * @return string
     */
    public function generate(UploadContext $context): string;
}
