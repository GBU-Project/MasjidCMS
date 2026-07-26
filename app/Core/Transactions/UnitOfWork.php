<?php

namespace App\Core\Transactions;

use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;

/**
 * Class UnitOfWork
 *
 * Implementasi Unit of Work Pattern untuk mengkoordinasikan pelacakan perubahan entitas dan eksekusi transaksi.
 */
class UnitOfWork implements UnitOfWorkInterface
{
    protected TransactionManagerInterface $transactionManager;

    /**
     * @var array<object>
     */
    protected array $newEntities = [];

    /**
     * @var array<object>
     */
    protected array $dirtyEntities = [];

    /**
     * @var array<object>
     */
    protected array $deletedEntities = [];

    public function __construct(?TransactionManagerInterface $transactionManager = null)
    {
        $this->transactionManager = $transactionManager ?? new DatabaseTransactionManager();
    }

    public function registerNew(object $entity): void
    {
        $this->newEntities[] = $entity;
    }

    public function registerDirty(object $entity): void
    {
        $this->dirtyEntities[] = $entity;
    }

    public function registerDeleted(object $entity): void
    {
        $this->deletedEntities[] = $entity;
    }

    public function commit(): void
    {
        $this->transactionManager->begin();

        try {
            // Commit transaction boundary
            $this->transactionManager->commit();
            $this->clear();
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function rollback(): void
    {
        $this->transactionManager->rollback();
        $this->clear();
    }

    public function clear(): void
    {
        $this->newEntities = [];
        $this->dirtyEntities = [];
        $this->deletedEntities = [];
    }
}
