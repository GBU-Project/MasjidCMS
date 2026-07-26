<?php

namespace App\Core\Contracts\Storage;

/**
 * Interface StorageProviderInterface
 *
 * Kontrak standar untuk penyedia media storage (Local Filesystem, S3, MinIO, dll).
 */
interface StorageProviderInterface
{
    /**
     * Menyimpan file ke storage.
     *
     * @param string $path Target path relatif penyimpanan
     * @param string $contents Isi konten mentah file
     * @param string $visibility Hak akses file ('public' atau 'private')
     * @return bool
     */
    public function store(string $path, string $contents, string $visibility = 'public'): bool;

    /**
     * Membaca isi konten file dari storage.
     *
     * @param string $path
     * @return string|null
     */
    public function retrieve(string $path): ?string;

    /**
     * Menghapus file dari storage.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;

    /**
     * Memeriksa keberadaan file di storage.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Mendapatkan URL publik untuk mengakses file.
     *
     * @param string $path
     * @return string
     */
    public function url(string $path): string;

    /**
     * Mendapatkan temporary signed URL untuk file privat.
     *
     * @param string $path
     * @param int $expirationMinutes Durasi masa berlaku URL (menit)
     * @return string
     */
    public function temporaryUrl(string $path, int $expirationMinutes = 60): string;
}
