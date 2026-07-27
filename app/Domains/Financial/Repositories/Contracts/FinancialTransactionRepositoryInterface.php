<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;

interface FinancialTransactionRepositoryInterface
{
    public function findById(int $id): ?FinancialTransaction;
    public function findByUuid(string $uuid): ?FinancialTransaction;
    public function findByTransactionNo(TransactionNumber $transactionNo): ?FinancialTransaction;
    public function save(FinancialTransaction $transaction): FinancialTransaction;
}
