<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\FinancialAccount;

interface FinancialAccountRepositoryInterface
{
    public function findById(int $id): ?FinancialAccount;
    public function findByIdForUpdate(int $id): ?FinancialAccount;
    public function findByUuid(string $uuid): ?FinancialAccount;
    public function findByCode(string $code): ?FinancialAccount;
    public function save(FinancialAccount $account): FinancialAccount;
}
