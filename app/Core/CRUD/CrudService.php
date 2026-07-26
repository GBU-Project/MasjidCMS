<?php

namespace App\Core\CRUD;

use App\Core\Contracts\CrudRepositoryInterface;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;
use App\Core\Contracts\Validation\ValidatorInterface;
use App\Core\Events\EntityCreatedEvent;
use App\Core\Events\EntityDeletedEvent;
use App\Core\Events\EntityUpdatedEvent;
use App\Core\Events\EventDispatcher;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\BaseService;
use App\Core\Transactions\DatabaseTransactionManager;
use App\Core\Transactions\UnitOfWork;
use Throwable;

/**
 * Class CrudService
 *
 * Abstract Generic Service Engine penyedia siklus hidup CRUD standar untuk seluruh Domain.
 * Terintegrasi dengan Validation Engine (ValidatorInterface), Lifecycle Hooks, Transaction Boundary, & Event Engine.
 */
abstract class CrudService extends BaseService
{
    protected CrudRepositoryInterface $repository;
    protected EventDispatcherInterface $dispatcher;
    protected TransactionManagerInterface $transactionManager;
    protected UnitOfWorkInterface $unitOfWork;
    protected ?ValidatorInterface $domainValidator = null;
    protected string $entityName = 'Entity';

    public function __construct(
        CrudRepositoryInterface $repository,
        ?EventDispatcherInterface $dispatcher = null,
        ?TransactionManagerInterface $transactionManager = null,
        ?UnitOfWorkInterface $unitOfWork = null,
        ?ValidatorInterface $validator = null
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->dispatcher = $dispatcher ?? new EventDispatcher();
        $this->transactionManager = $transactionManager ?? new DatabaseTransactionManager();
        $this->unitOfWork = $unitOfWork ?? new UnitOfWork($this->transactionManager);
        $this->domainValidator = $validator;
    }

    /**
     * Memproses operasi pembuatan data baru (Create).
     */
    public function create(array|object $dto): mixed
    {
        $data = is_object($dto) ? (array) $dto : $dto;

        // 1. Validation Hook
        $this->validateCreate($data);

        // 2. Begin Transaction
        $this->transactionManager->begin();

        try {
            // 3. Lifecycle Hook Before Create
            $this->beforeCreate($data);

            // 4. Database Execution
            $result = $this->repository->create($data);

            // 5. Register to Unit of Work
            if (is_object($result)) {
                $this->unitOfWork->registerNew($result);
            }

            // 6. Commit Transaction
            $this->transactionManager->commit();

            // 7. Lifecycle Hook After Create & Event Dispatch
            $this->afterCreate($result);

            return $result;
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            $this->unitOfWork->rollback();
            $this->logError('Create operation failed, transaction rolled back: ' . $e->getMessage());
            throw $e;
        }
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

        // 2. Begin Transaction
        $this->transactionManager->begin();

        try {
            // 3. Lifecycle Hook Before Update
            $this->beforeUpdate($id, $data);

            // 4. Database Execution
            $this->repository->update($id, $data);
            $updatedEntity = $this->find($id);

            // 5. Register to Unit of Work
            if (is_object($updatedEntity)) {
                $this->unitOfWork->registerDirty($updatedEntity);
            }

            // 6. Commit Transaction
            $this->transactionManager->commit();

            // 7. Lifecycle Hook After Update & Event Dispatch
            $this->afterUpdate($updatedEntity);

            return $updatedEntity;
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            $this->unitOfWork->rollback();
            $this->logError('Update operation failed, transaction rolled back: ' . $e->getMessage());
            throw $e;
        }
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

        // 2. Begin Transaction
        $this->transactionManager->begin();

        try {
            // 3. Lifecycle Hook Before Delete
            $this->beforeDelete($id);

            // 4. Database Execution
            $result = $this->repository->delete($id);

            // 5. Commit Transaction
            $this->transactionManager->commit();

            // 6. Lifecycle Hook After Delete & Event Dispatch
            $this->afterDelete($id);

            return $result;
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            $this->unitOfWork->rollback();
            $this->logError('Delete operation failed, transaction rolled back: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Memproses operasi pemulihan data terhapus (Restore).
     */
    public function restore(int|string $id): bool
    {
        $this->transactionManager->begin();

        try {
            // 1. Lifecycle Hook Before Restore
            $this->beforeRestore($id);

            // 2. Database Execution
            $result = $this->repository->restore($id);

            // 3. Commit Transaction
            $this->transactionManager->commit();

            // 4. Lifecycle Hook After Restore
            $this->afterRestore($id);

            return $result;
        } catch (Throwable $e) {
            $this->transactionManager->rollback();
            $this->unitOfWork->rollback();
            throw $e;
        }
    }

    /**
     * Helper eksekusi ValidatorEngine untuk memverifikasi data input.
     *
     * @param array $data
     * @param ValidatorInterface|null $validator
     * @return void
     * @throws ValidationException
     */
    protected function validateWith(array $data, ?ValidatorInterface $validator = null): void
    {
        $v = $validator ?? $this->domainValidator;

        if ($v !== null) {
            $result = $v->validate($data);

            if (!$result->isValid()) {
                throw new ValidationException('Validation failed', $result->errors);
            }
        }
    }

    public function find(int|string $id): mixed
    {
        return $this->repository->find($id);
    }

    public function findAll(array $filters = [], array $sort = []): array
    {
        return $this->repository->findAll($filters, $sort);
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginate($page, $perPage);
    }

    public function exists(int|string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }

    // =========================================================================
    // VALIDATION HOOKS (Child Services override if needed)
    // =========================================================================

    protected function validateCreate(array $data): void
    {
        $this->validateWith($data);
    }

    protected function validateUpdate(int|string $id, array $data): void
    {
        $this->validateWith($data);
    }

    protected function validateDelete(int|string $id): void {}

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    protected function beforeCreate(array &$data): void {}

    protected function afterCreate(mixed $entity): void
    {
        $this->dispatcher->dispatch(new EntityCreatedEvent($this->entityName, $entity));
    }

    protected function beforeUpdate(int|string $id, array &$data): void {}

    protected function afterUpdate(mixed $entity): void
    {
        $this->dispatcher->dispatch(new EntityUpdatedEvent($this->entityName, $entity));
    }

    protected function beforeDelete(int|string $id): void {}

    protected function afterDelete(int|string $id): void
    {
        $this->dispatcher->dispatch(new EntityDeletedEvent($this->entityName, $id));
    }

    protected function beforeRestore(int|string $id): void {}
    protected function afterRestore(int|string $id): void {}
}
