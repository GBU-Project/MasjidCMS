<?php

namespace App\Core\Traits;

/**
 * Trait UserStampTrait
 *
 * Skeleton trait untuk pencatatan otomatis user pembuat/pengubah data (created_by, updated_by).
 */
trait UserStampTrait
{
    /**
     * Memperoleh ID user aktif yang sedang login (Skeleton).
     *
     * @return int|string|null
     */
    protected function getCurrentUserId(): int|string|null
    {
        // Skeleton logic - akan diintegrasikan dengan Auth Service di masa depan
        return null;
    }
}
