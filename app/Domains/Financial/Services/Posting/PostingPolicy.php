<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Posting;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class PostingPolicy
{
    public function canPost(FinancialTransaction $transaction): bool
    {
        if ($transaction->isImmutable()) {
            return false;
        }

        return in_array($transaction->getStatus(), ['DRAFT', 'APPROVED'], true);
    }

    public function assertCanPost(FinancialTransaction $transaction): void
    {
        if (!$this->canPost($transaction)) {
            throw new BusinessRuleException(
                "Posting Policy Rejection: Transaksi [{$transaction->getTransactionNo()->getValue()}] berstatus [{$transaction->getStatus()}] tidak dapat di-post."
            );
        }
    }
}
