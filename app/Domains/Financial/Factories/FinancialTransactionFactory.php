<?php

declare(strict_types=1);

namespace App\Domains\Financial\Factories;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;

class FinancialTransactionFactory
{
    public static function createDraft(
        string $uuid,
        string $masjidId,
        int $fundId,
        int $accountId,
        int $financialAccountId,
        TransactionNumber $transactionNo,
        string $transactionType,
        Money $amount,
        string $transactionDate,
        ?int $programId = null,
        ?string $jamaahId = null,
        ?string $description = null,
        ?string $createdBy = null
    ): FinancialTransaction {
        return new FinancialTransaction(
            null,
            $uuid,
            $masjidId,
            $fundId,
            $accountId,
            $financialAccountId,
            $transactionNo,
            $transactionType,
            $amount,
            $transactionDate,
            $programId,
            $jamaahId,
            null,
            null,
            null,
            'CASH',
            'DRAFT',
            $description,
            $createdBy
        );
    }
}
