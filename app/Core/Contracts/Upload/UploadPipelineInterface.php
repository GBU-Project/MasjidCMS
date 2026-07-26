<?php

namespace App\Core\Contracts\Upload;

use App\Core\Storage\Media;
use App\Core\Upload\UploadContext;

/**
 * Interface UploadPipelineInterface
 *
 * Kontrak alur pemrosesan upload berkas (Upload Pipeline) sebelum diserahkan ke Storage Engine.
 */
interface UploadPipelineInterface
{
    /**
     * Memproses konteks berkas upload melalui pipeline.
     *
     * @param UploadContext $context
     * @return Media
     */
    public function process(UploadContext $context): Media;
}
