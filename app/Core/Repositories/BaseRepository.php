<?php

namespace App\Core\Repositories;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Class BaseRepository
 *
 * Parent class untuk seluruh Repository layer di MasjidCMS.
 * Menyediakan utilitas pembentuk query (query builder), pagination, filtering, dan sorting.
 */
abstract class BaseRepository
{
    /**
     * @var BaseConnection
     */
    protected BaseConnection $db;

    /**
     * Nama tabel utama yang dikelola oleh repository konkret.
     *
     * @var string
     */
    protected string $table = '';

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * Mendapatkan Query Builder untuk tabel utama.
     *
     * @param string|null $table
     * @return BaseBuilder
     */
    protected function builder(?string $table = null): BaseBuilder
    {
        $tableName = $table ?? $this->table;

        if (empty($tableName)) {
            throw new \InvalidArgumentException('Table name is not defined in repository.');
        }

        return $this->db->table($tableName);
    }

    /**
     * Helper pagination sederhana.
     *
     * @param BaseBuilder $builder
     * @param int $page
     * @param int $perPage
     * @return array{data: array, total: int, page: int, per_page: int, last_page: int}
     */
    protected function paginate(BaseBuilder $builder, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        // Count total rows using cloned builder to avoid modifying the original query state
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $offset = ($page - 1) * $perPage;
        $data = $builder->limit($perPage, $offset)->get()->getResultArray();

        $lastPage = (int) ceil($total / $perPage);

        return [
            'data'      => $data,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage > 0 ? $lastPage : 1,
        ];
    }

    /**
     * Helper penyaringan (filtering) query berdasarkan kriteria.
     *
     * @param BaseBuilder $builder
     * @param array $filters Key-value pasangan kolom dan nilai filter
     * @param array $allowedColumns Kolom yang diizinkan untuk difilter
     * @return BaseBuilder
     */
    protected function applyFilters(BaseBuilder $builder, array $filters, array $allowedColumns = []): BaseBuilder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (!empty($allowedColumns) && !in_array($field, $allowedColumns, true)) {
                continue;
            }

            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        return $builder;
    }

    /**
     * Helper pengurutan (sorting) query.
     *
     * @param BaseBuilder $builder
     * @param string $sortBy
     * @param string $sortOrder ('ASC' atau 'DESC')
     * @param array $allowedSortColumns
     * @param string $defaultSort
     * @return BaseBuilder
     */
    protected function applySorting(
        BaseBuilder $builder,
        string $sortBy = 'created_at',
        string $sortOrder = 'DESC',
        array $allowedSortColumns = [],
        string $defaultSort = 'created_at'
    ): BaseBuilder {
        if (!empty($allowedSortColumns) && !in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = $defaultSort;
        }

        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        return $builder->orderBy($sortBy, $order);
    }
}
