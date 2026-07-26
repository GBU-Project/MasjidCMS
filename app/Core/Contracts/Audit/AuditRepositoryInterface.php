<?php

namespace App\Core\Contracts\Audit;

use App\Core\Audit\AuditEntry;

/**
 * Interface AuditRepositoryInterface
 *
 * Kontrak simpanan log audit (Audit Trail & Activity Log).
 */
interface AuditRepositoryInterface
{
    /**
     * Menyimpan log entri audit baru.
     *
     * @param AuditEntry $entry
     * @return bool
     */
    public function store(AuditEntry $entry): bool;

    /**
     * Mencari entri audit berdasarkan ID.
     *
     * @param int|string $id
     * @return AuditEntry|null
     */
    public function find(int|string $id): ?AuditEntry;

    /**
     * Membaca entri audit terpaginasi.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginate(int $page = 1, int $perPage = 15): array;
}
