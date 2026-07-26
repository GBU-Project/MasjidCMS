<?php

namespace App\Core\Repositories;

use App\Core\Contracts\CrudRepositoryInterface;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Class BaseRepository
 *
 * Parent class untuk seluruh Repository layer di MasjidCMS.
 * Mengimplementasikan CrudRepositoryInterface untuk dukungan Generic CRUD Engine.
 */
abstract class BaseRepository implements CrudRepositoryInterface
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
     * Menyimpan data baru.
     */
    public function create(array $data): mixed
    {
        $this->builder()->insert($data);
        return $this->db->insertID();
    }

    /**
     * Memperbarui data berdasarkan ID.
     */
    public function update(int|string $id, array $data): bool
    {
        return $this->builder()->where('id', $id)->update($data);
    }

    /**
     * Menghapus data berdasarkan ID.
     */
    public function delete(int|string $id): bool
    {
        return $this->builder()->where('id', $id)->delete();
    }

    /**
     * Restorasi data terhapus (Soft delete placeholder).
     */
    public function restore(int|string $id): bool
    {
        return $this->builder()->where('id', $id)->update(['deleted_at' => null]);
    }

    /**
     * Mencari satu baris data berdasarkan ID.
     */
    public function find(int|string $id): mixed
    {
        return $this->builder()->where('id', $id)->get()->getRowArray();
    }

    /**
     * Mendapatkan seluruh data dengan filter opsional.
     */
    public function findAll(array $filters = [], array $sort = []): array
    {
        $builder = $this->builder();
        $builder = $this->applyFilters($builder, $filters);

        if (!empty($sort['by'])) {
            $builder = $this->applySorting($builder, $sort['by'], $sort['order'] ?? 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Helper pagination standar.
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $builder = $this->builder();
        return $this->paginateBuilder($builder, $page, $perPage);
    }

    /**
     * Pagination khusus builder.
     */
    protected function paginateBuilder(BaseBuilder $builder, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

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
     * Memeriksa keberadaan data berdasarkan ID.
     */
    public function exists(int|string $id): bool
    {
        return $this->builder()->where('id', $id)->countAllResults() > 0;
    }

    /**
     * Menghitung total baris data.
     */
    public function count(array $filters = []): int
    {
        $builder = $this->builder();
        $builder = $this->applyFilters($builder, $filters);
        return $builder->countAllResults();
    }

    /**
     * Helper penyaringan query.
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
     * Helper pengurutan query.
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
