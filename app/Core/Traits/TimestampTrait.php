<?php

namespace App\Core\Traits;

/**
 * Trait TimestampTrait
 *
 * Skeleton trait untuk penanganan otomatis kolom timestamp (created_at, updated_at).
 */
trait TimestampTrait
{
    /**
     * Memformat tanggal saat ini untuk timestamp.
     *
     * @param string $format
     * @return string
     */
    protected function getCurrentTimestamp(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}
