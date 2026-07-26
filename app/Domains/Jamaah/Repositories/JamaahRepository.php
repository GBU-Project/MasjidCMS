<?php

namespace App\Domains\Jamaah\Repositories;

use App\Core\Repositories\BaseRepository;

/**
 * Class JamaahRepository
 *
 * Repository tunggal penangan data domain Jamaah.
 */
class JamaahRepository extends BaseRepository
{
    protected string $table = 'jamaahs';

    public function findByCode(string $code): ?array
    {
        $row = $this->builder()->where('code', $code)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $row = $this->builder()->where('slug', $slug)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    public function isUniqueExcept(string $field, string $value, int|string|null $exceptId = null): bool
    {
        $builder = $this->builder()->where($field, $value)->where('deleted_at', null);

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() === 0;
    }
}
