<?php

namespace App\Core\Transactions;

use App\Core\Contracts\Transactions\TransactionManagerInterface;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;
use Throwable;

/**
 * Class DatabaseTransactionManager
 *
 * Implementasi TransactionManagerInterface menggunakan Driver Transaksi Database CodeIgniter 4.
 */
class DatabaseTransactionManager implements TransactionManagerInterface
{
    protected BaseConnection $db;
    protected bool $inTransaction = false;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    /**
     * Memulai transaksi database.
     */
    public function begin(): void
    {
        $this->db->transBegin();
        $this->inTransaction = true;
    }

    /**
     * Menyelesaikan transaksi.
     */
    public function commit(): void
    {
        if (!$this->inTransaction) {
            return;
        }

        if ($this->db->transStatus() === false) {
            $this->rollback();
            throw new RuntimeException('Database transaction status check failed during commit.');
        }

        $this->db->transCommit();
        $this->inTransaction = false;
    }

    /**
     * Membatalkan transaksi.
     */
    public function rollback(): void
    {
        if ($this->inTransaction) {
            $this->db->transRollback();
            $this->inTransaction = false;
        }
    }

    /**
     * Wrapper transaksi otomatis.
     */
    public function transaction(callable $callback): mixed
    {
        $this->begin();

        try {
            $result = $callback($this->db);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Memeriksa status aktif transaksi.
     */
    public function isActive(): bool
    {
        return $this->inTransaction;
    }
}
