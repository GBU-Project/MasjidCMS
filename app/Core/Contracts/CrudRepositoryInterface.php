<?php

namespace App\Core\Contracts;

/**
 * Interface CrudRepositoryInterface
 *
 * Kontrak standar umum untuk seluruh Repository layer yang mendukung operasi CRUD di MasjidCMS.
 */
interface CrudRepositoryInterface
{
    /**
     * Menyimpan data baru ke basis data.
     *
     * @param array $data
     * @return int|string|array|object
     */
    public function create(array $data): mixed;

    /**
     * Memperbarui data berdasarkan Primary ID.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Menghapus data berdasarkan Primary ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Mengembalikan data yang terhapus (Soft Delete Restore).
     *
     * @param int|string $id
     * @return bool
     */
    public function restore(int|string $id): bool;

    /**
     * Mencari data berdasarkan Primary ID.
     *
     * @param int|string $id
     * @return mixed
     */
    public function find(int|string $id): mixed;

    /**
     * Mendapatkan seluruh data dengan filter dan sorting opsional.
     *
     * @param array $filters
     * @param array $sort
     * @return array
     */
    public function findAll(array $filters = [], array $sort = []): array;

    /**
     * Mendapatkan data terpaginasi.
     *
     * @param int $page
     * @param int $perPage
     * @return array{data: array, total: int, page: int, per_page: int, last_page: int}
     */
    public function paginate(int $page = 1, int $perPage = 15): array;

    /**
     * Memeriksa keberadaan data berdasarkan ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function exists(int|string $id): bool;

    /**
     * Menghitung total baris data berdasarkan filter.
     *
     * @param array $filters
     * @return int
     */
    public function count(array $filters = []): int;
}
