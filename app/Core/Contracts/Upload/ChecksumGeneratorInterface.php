<?php

namespace App\Core\Contracts\Upload;

/**
 * Interface ChecksumGeneratorInterface
 *
 * Kontrak perhitungan checksum integritas data berkas.
 */
interface ChecksumGeneratorInterface
{
    /**
     * Menghitung nilai checksum dari konten mentah atau file path.
     *
     * @param string $contentsOrPath
     * @return string
     */
    public function generate(string $contentsOrPath): string;
}
