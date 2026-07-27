<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Posting;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class PostingValidator
{
    public function validate(
        FinancialTransaction $transaction,
        Fund $fund,
        FinancialAccount $financialAccount
    ): void {
        // 1. Validate Amount Positivity
        if ($transaction->getAmount()->getAmount() <= 0) {
            throw new BusinessRuleException("Validation Error: Nominal transaksi harus lebih besar dari 0.");
        }

        // 2. Validate Transaction Date Format
        if (empty($transaction->getTransactionDate())) {
            throw new BusinessRuleException("Validation Error: Tanggal transaksi wajib diisi.");
        }

        // 3. Fund Restriction Rule Checks
        if ($fund->getFundCode()->getValue() === 'ZAKAT' && $transaction->getTransactionType() === 'EXPENSE' && $transaction->getAccountId() === 501) {
            // Example strict check: Zakat fund expense for physical operational overhead is prohibited
            throw new BusinessRuleException("BR-FIN-01 Violation: Dana Zakat dilarang digunakan untuk beban operasional umum.");
        }

        // 4. Deficit Check for Expenses
        if ($transaction->getTransactionType() === 'EXPENSE') {
            if ($financialAccount->getBalance()->getAmount() < $transaction->getAmount()->getAmount()) {
                throw new BusinessRuleException(
                    "BR-FIN-04 Violation: Saldo kas [{$financialAccount->getCode()}] (Rp {$financialAccount->getBalance()->getAmount()}) tidak mencukupi untuk pengeluaran (Rp {$transaction->getAmount()->getAmount()})."
                );
            }
        }
    }
}
