<?php

namespace App\Core\Contracts\Transactions;

/**
 * Interface TransactionManagerInterface
 *
 * Kontrak manajemen transaksi database untuk mengisolasi batas transaksi (Transaction Boundary).
 */
interface TransactionManagerInterface
{
    /**
     * Memulai transaksi database (Begin Transaction).
     *
     * @return void
     */
    public function begin(): void;

    /**
     * Menyelesaikan dan menyimpan seluruh perubahan transaksi (Commit).
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Membatalkan seluruh perubahan transaksi (Rollback).
     *
     * @return void
     */
    public function rollback(): void;

    /**
     * Wrapper eksekusi callback dalam batas transaksi otomatis.
     *
     * @param callable $callback
     * @return mixed
     */
    public function transaction(callable $callback): mixed;

    /**
     * Memeriksa apakah transaksi sedang aktif.
     *
     * @return bool
     */
    public function isActive(): bool;
}
