<?php

namespace App\Core\Upload;

/**
 * Class UploadContext
 *
 * Context DTO penampung state & metadata berkas selama proses Upload Pipeline.
 */
class UploadContext
{
    public string $checksum = '';
    public string $generatedFilename = '';
    public string $targetPath = '';

    public function __construct(
        public readonly mixed $uploadedFile = null,
        public readonly string $fileContents = '',
        public readonly string $originalName = '',
        public readonly string $mimeType = 'application/octet-stream',
        public readonly string $extension = '',
        public readonly int $size = 0,
        public readonly string $visibility = 'public',
        public readonly string $targetDisk = 'local',
        public readonly string $targetDirectory = 'general',
        public readonly array $allowedMimeTypes = [],
        public readonly array $allowedExtensions = [],
        public readonly int $maxSizeBytes = 10485760 // 10MB default
    ) {}
}
