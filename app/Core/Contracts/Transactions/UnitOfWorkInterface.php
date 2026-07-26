<?php

namespace App\Core\Contracts\Transactions;

/**
 * Interface UnitOfWorkInterface
 *
 * Kontrak Unit of Work Pattern untuk mengumpulkan, melacak perubahan entitas, dan mengeksekusinya dalam satu transaksi utuh.
 */
interface UnitOfWorkInterface
{
    /**
     * Mendaftarkan entitas baru yang akan disimpan.
     *
     * @param object $entity
     * @return void
     */
    public function registerNew(object $entity): void;

    /**
     * Mendaftarkan entitas lama yang diubah.
     *
     * @param object $entity
     * @return void
     */
    public function registerDirty(object $entity): void;

    /**
     * Mendaftarkan entitas yang akan dihapus.
     *
     * @param object $entity
     * @return void
     */
    public function registerDeleted(object $entity): void;

    /**
     * Mengeksekusi commit seluruh entitas terdaftar dalam satu transaksi.
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Membatalkan seluruh pendaftaran entitas dan melakukan rollback.
     *
     * @return void
     */
    public function rollback(): void;

    /**
     * Membersihkan daftar lacak entitas.
     *
     * @return void
     */
    public function clear(): void;
}
