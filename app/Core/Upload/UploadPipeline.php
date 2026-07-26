<?php

namespace App\Core\Upload;

use App\Core\Contracts\Upload\ChecksumGeneratorInterface;
use App\Core\Contracts\Upload\FilenameGeneratorInterface;
use App\Core\Contracts\Upload\UploadPipelineInterface;
use App\Core\Exceptions\ValidationException;
use App\Core\Storage\Media;
use App\Core\Storage\Services\StorageService;
use App\Core\Upload\Generators\Sha256ChecksumGenerator;
use App\Core\Upload\Generators\UuidFilenameGenerator;

/**
 * Class UploadPipeline
 *
 * Implemetasi Pipeline persiapan dan penyaringan berkas sebelum diserahkan ke Storage Engine.
 */
class UploadPipeline implements UploadPipelineInterface
{
    protected StorageService $storageService;
    protected FilenameGeneratorInterface $filenameGenerator;
    protected ChecksumGeneratorInterface $checksumGenerator;

    public function __construct(
        ?StorageService $storageService = null,
        ?FilenameGeneratorInterface $filenameGenerator = null,
        ?ChecksumGeneratorInterface $checksumGenerator = null
    ) {
        $this->storageService = $storageService ?? new StorageService();
        $this->filenameGenerator = $filenameGenerator ?? new UuidFilenameGenerator();
        $this->checksumGenerator = $checksumGenerator ?? new Sha256ChecksumGenerator();
    }

    /**
     * Memproses berkas melalui urutan pipeline resmi.
     */
    public function process(UploadContext $context): Media
    {
        // Step 1: Verify File Availability
        $this->verifyFile($context);

        // Step 2: Validate MIME Type
        $this->validateMime($context);

        // Step 3: Validate File Extension
        $this->validateExtension($context);

        // Step 4: Validate File Size
        $this->validateSize($context);

        // Step 5: Generate Checksum
        $this->generateChecksum($context);

        // Step 6: Generate Unique Filename & Target Path
        $this->generateFilename($context);

        // Step 7: Store File via StorageService
        return $this->store($context);
    }

    protected function verifyFile(UploadContext $context): void
    {
        if (empty($context->fileContents) && empty($context->uploadedFile)) {
            throw new ValidationException('Upload failed: File contents or file payload is missing.');
        }
    }

    protected function validateMime(UploadContext $context): void
    {
        if (!empty($context->allowedMimeTypes) && !in_array($context->mimeType, $context->allowedMimeTypes, true)) {
            throw new ValidationException(sprintf('Invalid file type [%s]. Allowed MIME types: %s', $context->mimeType, implode(', ', $context->allowedMimeTypes)));
        }
    }

    protected function validateExtension(UploadContext $context): void
    {
        if (!empty($context->allowedExtensions) && !in_array(strtolower($context->extension), array_map('strtolower', $context->allowedExtensions), true)) {
            throw new ValidationException(sprintf('Invalid extension [%s]. Allowed extensions: %s', $context->extension, implode(', ', $context->allowedExtensions)));
        }
    }

    protected function validateSize(UploadContext $context): void
    {
        if ($context->maxSizeBytes > 0 && $context->size > $context->maxSizeBytes) {
            throw new ValidationException(sprintf('File size [%d bytes] exceeds maximum allowed size [%d bytes].', $context->size, $context->maxSizeBytes));
        }
    }

    protected function generateChecksum(UploadContext $context): void
    {
        $context->checksum = $this->checksumGenerator->generate($context->fileContents);
    }

    protected function generateFilename(UploadContext $context): void
    {
        $context->generatedFilename = $this->filenameGenerator->generate($context);
        $directory = trim($context->targetDirectory, '/\\');
        $context->targetPath = !empty($directory) ? $directory . '/' . $context->generatedFilename : $context->generatedFilename;
    }

    protected function store(UploadContext $context): Media
    {
        return $this->storageService->store(
            path: $context->targetPath,
            contents: $context->fileContents,
            visibility: $context->visibility
        );
    }
}
