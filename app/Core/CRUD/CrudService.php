<?php

namespace App\Core\CRUD;

use App\Core\Contracts\CrudRepositoryInterface;
use App\Core\Exceptions\NotFoundException;
use App\Core\Services\BaseService;

/**
 * Class CrudService
 *
 * Abstract Generic Service Engine penyedia siklus hidup CRUD standar untuk seluruh Domain.
 * Dilengkapi dengan Validation Hooks & Lifecycle Hooks.
 */
abstract class CrudService extends BaseService
{
    protected CrudRepositoryInterface $repository;

    public function __construct(CrudRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Memproses operasi pembuat data baru (Create).
     */
    public function create(array|object $dto): mixed
    {
        $data = is_object($dto) ? (array) $dto : $dto;

        // 1. Validation Hook
        $this->validateCreate($data);

        // 2. Lifecycle Hook Before Create
        $this->beforeCreate($data);

        // 3. Database Execution via Repository Interface
        $result = $this->repository->create($data);

        // 4. Lifecycle Hook After Create
        $this->afterCreate($result);

        return $result;
    }

    /**
     * Memproses operasi pembaruan data (Update).
     */
    public function update(int|string $id, array|object $dto): mixed
    {
        if (!$this->exists($id)) {
            throw new NotFoundException(sprintf('Resource with ID [%s] not found.', (string) $id));
        }

        $data = is_object($dto) ? (array) $dto : $dto;

        // 1. Validation Hook
        $this->validateUpdate($id, $data);

        // 2. Lifecycle Hook Before Update
        $this->beforeUpdate($id, $data);

        // 3. Database Execution
        $this->repository->update($id, $data);
        $updatedEntity = $this->find($id);

        // 4. Lifecycle Hook After Update
        $this->afterUpdate($updatedEntity);

        return $updatedEntity;
    }

    /**
     * Memproses operasi penghapusan data (Delete).
     */
    public function delete(int|string $id): bool
    {
        if (!$this->exists($id)) {
            throw new NotFoundException(sprintf('Resource with ID [%s] not found.', (string) $id));
        }

        // 1. Validation Hook
        $this->validateDelete($id);

        // 2. Lifecycle Hook Before Delete
        $this->beforeDelete($id);

        // 3. Database Execution
        $result = $this->repository->delete($id);

        // 4. Lifecycle Hook After Delete
        $this->afterDelete($id);

        return $result;
    }

    /**
     * Memproses operasi pemulihan data terhapus (Restore).
     */
    public function restore(int|string $id): bool
    {
        // 1. Lifecycle Hook Before Restore
        $this->beforeRestore($id);

        // 2. Database Execution
        $result = $this->repository->restore($id);

        // 3. Lifecycle Hook After Restore
        $this->afterRestore($id);

        return $result;
    }

    /**
     * Mencari data tunggal berdasarkan ID.
     */
    public function find(int|string $id): mixed
    {
        return $this->repository->find($id);
    }

    /**
     * Membaca seluruh data.
     */
    public function findAll(array $filters = [], array $sort = []): array
    {
        return $this->repository->findAll($filters, $sort);
    }

    /**
     * Membaca data terpaginasi.
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginate($page, $perPage);
    }

    /**
     * Memeriksa keberadaan data.
     */
    public function exists(int|string $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Menghitung total data.
     */
    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }

    // =========================================================================
    // VALIDATION HOOKS (Child Service override if needed)
    // =========================================================================

    protected function validateCreate(array $data): void {}
    protected function validateUpdate(int|string $id, array $data): void {}
    protected function validateDelete(int|string $id): void {}

    // =========================================================================
    // LIFECYCLE HOOKS (Child Service override if needed - Default No-op)
    // =========================================================================

    protected function beforeCreate(array &$data): void {}
    protected function afterCreate(mixed $entity): void {}
    protected function beforeUpdate(int|string $id, array &$data): void {}
    protected function afterUpdate(mixed $entity): void {}
    protected function beforeDelete(int|string $id): void {}
    protected function afterDelete(int|string $id): void {}
    protected function beforeRestore(int|string $id): void {}
    protected function afterRestore(int|string $id): void {}
}
