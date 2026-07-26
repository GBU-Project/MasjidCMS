<?php

namespace App\Domains\Jamaah\Repositories;

use App\Core\Repositories\BaseRepository;

/**
 * Class JamaahRepository
 *
 * Repository penangan data domain Jamaah.
 */
class JamaahRepository extends BaseRepository
{
    protected string $table = 'jamaahs';

    public function findByMemberNo(string $memberNo): ?array
    {
        $row = $this->builder()->where('member_no', $memberNo)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    public function findByNik(string $nik): ?array
    {
        $row = $this->builder()->where('nik', $nik)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $row = $this->builder()->where('email', $email)->where('deleted_at', null)->get()->getRowArray();
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

    /**
     * Paginasi dengan fitur pencarian (search), filter, dan pengurutan (sort).
     */
    public function searchAndPaginate(
        string $search = '',
        array $filters = [],
        array $sort = [],
        int $page = 1,
        int $perPage = 15
    ): array {
        $builder = $this->builder()->where('deleted_at', null);

        // 1. Search Query (LIKE match across multiple columns)
        if (!empty($search)) {
            $builder->groupStart()
                ->like('member_no', $search)
                ->orLike('nik', $search)
                ->orLike('full_name', $search)
                ->orLike('phone', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        // 2. Multi-column Filtering
        $allowedFilters = ['status', 'gender', 'city', 'district'];
        foreach ($allowedFilters as $filterKey) {
            if (!empty($filters[$filterKey])) {
                if (is_array($filters[$filterKey])) {
                    $builder->whereIn($filterKey, $filters[$filterKey]);
                } else {
                    $builder->where($filterKey, $filters[$filterKey]);
                }
            }
        }

        // 3. Sorting
        $sortBy = $sort['by'] ?? 'created_at';
        $sortOrder = strtoupper($sort['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $allowedSorts = ['full_name', 'name', 'member_no', 'created_at'];

        if ($sortBy === 'name') {
            $sortBy = 'full_name';
        }

        if (in_array($sortBy, $allowedSorts, true)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('created_at', 'DESC');
        }

        // 4. Paginate
        return $this->paginateBuilder($builder, $page, $perPage);
    }
}
