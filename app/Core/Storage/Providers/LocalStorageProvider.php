<?php

namespace App\Core\Storage\Providers;

use App\Core\Contracts\Storage\StorageProviderInterface;
use App\Core\Storage\Config\StorageConfig;

/**
 * Class LocalStorageProvider
 *
 * Implementasi Storage Provider berbasis lokal Filesystem (server lokal).
 */
class LocalStorageProvider implements StorageProviderInterface
{
    protected StorageConfig $config;

    public function __construct(?StorageConfig $config = null)
    {
        $this->config = $config ?? new StorageConfig();
    }

    /**
     * Menyimpan berkas ke filesystem lokal.
     */
    public function store(string $path, string $contents, string $visibility = 'public'): bool
    {
        $fullPath = $this->getFullPath($path, $visibility);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return file_put_contents($fullPath, $contents) !== false;
    }

    /**
     * Membaca berkas dari filesystem lokal.
     */
    public function retrieve(string $path): ?string
    {
        $fullPath = $this->resolvePath($path);

        if (!file_exists($fullPath)) {
            return null;
        }

        $content = file_get_contents($fullPath);
        return $content !== false ? $content : null;
    }

    /**
     * Menghapus berkas dari filesystem lokal.
     */
    public function delete(string $path): bool
    {
        $fullPath = $this->resolvePath($path);

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    /**
     * Memeriksa keberadaan berkas di filesystem lokal.
     */
    public function exists(string $path): bool
    {
        $fullPath = $this->resolvePath($path);
        return file_exists($fullPath);
    }

    /**
     * Mendapatkan URL publik untuk berkas.
     */
    public function url(string $path): string
    {
        $relativePath = ltrim($path, '/');
        return base_url('uploads/' . $relativePath);
    }

    /**
     * Mendapatkan temporary signed URL untuk berkas privat.
     */
    public function temporaryUrl(string $path, int $expirationMinutes = 60): string
    {
        $expires = time() + ($expirationMinutes * 60);
        $signature = hash_hmac('sha256', $path . $expires, config('App')->appKey ?? 'MasjidCMSSecret');

        return base_url(sprintf('storage/temp?path=%s&expires=%d&signature=%s', urlencode($path), $expires, $signature));
    }

    /**
     * Helper mendapatkan full system path berdasarkan visibility.
     */
    protected function getFullPath(string $path, string $visibility = 'public'): string
    {
        $relativePath = ltrim($path, '/\\');
        $baseDir = ($visibility === 'public') ? $this->config->public_path : $this->config->base_path;

        return rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR . $relativePath;
    }

    /**
     * Helper mendeteksi lokasi file di public atau private storage.
     */
    protected function resolvePath(string $path): string
    {
        $publicPath = $this->getFullPath($path, 'public');
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return $this->getFullPath($path, 'private');
    }
}
