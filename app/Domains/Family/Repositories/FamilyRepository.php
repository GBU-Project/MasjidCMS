<?php

namespace App\Domains\Family\Repositories;

use App\Core\Repositories\BaseRepository;

/**
 * Class FamilyRepository
 *
 * Repository penangan data domain Family (Keluarga).
 */
class FamilyRepository extends BaseRepository
{
    protected string $table = 'families';

    public function findByFamilyNo(string $familyNo): ?array
    {
        $row = $this->builder()->where('family_no', $familyNo)->where('deleted_at', null)->get()->getRowArray();
        return $row ?: null;
    }

    public function findByKkNumber(string $kkNumber): ?array
    {
        $row = $this->builder()->where('kk_number', $kkNumber)->where('deleted_at', null)->get()->getRowArray();
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
     * Mendapatkan daftar jamaah anggota keluarga dari tabel jamaahs.
     */
    public function findMembers(string $familyId): array
    {
        if (!$this->db->tableExists('jamaahs')) {
            return [];
        }

        return $this->db->table('jamaahs')
            ->where('family_id', $familyId)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
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

        // 1. Search Query
        if (!empty($search)) {
            $builder->groupStart()
                ->like('family_no', $search)
                ->orLike('kk_number', $search)
                ->orLike('name', $search)
                ->orLike('city', $search)
                ->orLike('district', $search)
                ->groupEnd();
        }

        // 2. Multi-column Filtering
        $allowedFilters = ['family_status', 'city', 'district'];
        foreach ($allowedFilters as $filterKey) {
            if (!empty($filters[$filterKey])) {
                $builder->where($filterKey, $filters[$filterKey]);
            }
        }

        // 3. Sorting
        $sortBy = $sort['by'] ?? 'created_at';
        $sortOrder = strtoupper($sort['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $allowedSorts = ['name', 'family_no', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $builder->orderBy($sortBy, $sortOrder);
        } else {
            $builder->orderBy('created_at', 'DESC');
        }

        return $this->paginateBuilder($builder, $page, $perPage);
    }
}
