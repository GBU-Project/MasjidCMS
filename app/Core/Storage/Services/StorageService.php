<?php

namespace App\Core\Storage\Services;

use App\Core\Contracts\Storage\StorageProviderInterface;
use App\Core\Contracts\Upload\UploadPipelineInterface;
use App\Core\Services\BaseService;
use App\Core\Storage\Config\StorageConfig;
use App\Core\Storage\Media;
use App\Core\Storage\Providers\StorageFactory;
use App\Core\Upload\UploadContext;

/**
 * Class StorageService
 *
 * Core Service penyedia manajemen berkas media storage di MasjidCMS.
 * Hanya bergantung pada StorageProviderInterface & UploadPipelineInterface.
 */
class StorageService extends BaseService
{
    protected StorageProviderInterface $provider;
    protected StorageConfig $config;

    public function __construct(
        ?StorageProviderInterface $provider = null,
        ?StorageConfig $config = null
    ) {
        parent::__construct();
        $this->config = $config ?? new StorageConfig();
        $this->provider = $provider ?? StorageFactory::create($this->config->default_provider, $this->config);
    }

    /**
     * Memproses upload file melalui Upload Pipeline resmi.
     */
    public function upload(UploadContext $context, ?UploadPipelineInterface $pipeline = null): Media
    {
        $uploadPipeline = $pipeline ?? new \App\Core\Upload\UploadPipeline($this);
        return $uploadPipeline->process($context);
    }

    /**
     * Menyimpan konten file baru ke media storage.
     */
    public function store(string $path, string $contents, string $visibility = 'public'): Media
    {
        $success = $this->provider->store($path, $contents, $visibility);

        if (!$success) {
            throw new \RuntimeException(sprintf('Failed to store file to path [%s]', $path));
        }

        $filename = basename($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $size = strlen($contents);
        $checksum = md5($contents);

        return new Media(
            disk: $this->config->default_provider,
            path: $path,
            filename: $filename,
            extension: $extension,
            mimeType: 'application/octet-stream',
            size: $size,
            checksum: $checksum,
            visibility: $visibility,
            createdAt: date('Y-m-d H:i:s')
        );
    }

    /**
     * Menghapus file dari media storage.
     */
    public function delete(string $path): bool
    {
        return $this->provider->delete($path);
    }

    /**
     * Memindahkan file dari satu path ke path lain.
     */
    public function move(string $fromPath, string $toPath): bool
    {
        $content = $this->provider->retrieve($fromPath);

        if ($content === null) {
            return false;
        }

        if ($this->provider->store($toPath, $content)) {
            $this->provider->delete($fromPath);
            return true;
        }

        return false;
    }

    /**
     * Menyalin file dari satu path ke path lain.
     */
    public function copy(string $fromPath, string $toPath): bool
    {
        $content = $this->provider->retrieve($fromPath);

        if ($content === null) {
            return false;
        }

        return $this->provider->store($toPath, $content);
    }

    /**
     * Memeriksa keberadaan file di media storage.
     */
    public function exists(string $path): bool
    {
        return $this->provider->exists($path);
    }

    /**
     * Mendapatkan URL publik file.
     */
    public function url(string $path): string
    {
        return $this->provider->url($path);
    }

    /**
     * Mendapatkan temporary signed URL untuk file privat.
     */
    public function temporaryUrl(string $path, int $expirationMinutes = 60): string
    {
        return $this->provider->temporaryUrl($path, $expirationMinutes);
    }
}
