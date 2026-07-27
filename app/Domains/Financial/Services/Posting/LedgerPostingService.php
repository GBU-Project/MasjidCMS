<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Posting;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\FinancialTransaction;

class LedgerPostingService
{
    public function updateCachedAccountBalance(
        FinancialAccount $account,
        FinancialTransaction $transaction
    ): void {
        if ($transaction->getTransactionType() === 'INCOME') {
            $account->credit($transaction->getAmount());
        } elseif ($transaction->getTransactionType() === 'EXPENSE') {
            $account->debit($transaction->getAmount());
        }
    }

    public function reverseAccountBalance(
        FinancialAccount $account,
        FinancialTransaction $transaction
    ): void {
        // Reverse cached balance
        if ($transaction->getTransactionType() === 'INCOME') {
            $account->debit($transaction->getAmount());
        } elseif ($transaction->getTransactionType() === 'EXPENSE') {
            $account->credit($transaction->getAmount());
        }
    }
}
