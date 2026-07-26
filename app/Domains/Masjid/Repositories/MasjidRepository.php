<?php

namespace App\Domains\Masjid\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\Masjid\Entities\Masjid;

/**
 * Class MasjidRepository
 *
 * Repository tunggal penangan data domain Masjid.
 */
class MasjidRepository extends BaseRepository
{
    protected string $table = 'masjids';

    /**
     * Cari masjid berdasarkan code.
     */
    public function findByCode(string $code): ?array
    {
        $row = $this->builder()->where('code', $code)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Cari masjid berdasarkan slug.
     */
    public function findBySlug(string $slug): ?array
    {
        $row = $this->builder()->where('slug', $slug)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Cari masjid berdasarkan email.
     */
    public function findByEmail(string $email): ?array
    {
        $row = $this->builder()->where('email', $email)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Verifikasi keunikan field terpisah dari ID tertentu.
     */
    public function isUniqueExcept(string $field, string $value, int|string|null $exceptId = null): bool
    {
        $builder = $this->builder()->where($field, $value)->where('deleted_at', null);

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() === 0;
    }
}
