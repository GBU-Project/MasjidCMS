<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\Fund;

use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class FinancialDomainService
{
    /**
     * BR-FIN-01: Dana Zakat dilarang ditransfer untuk Operasional fisik masjid
     */
    public function validateInterFundTransfer(Fund $sourceFund, Fund $targetFund, Money $amount): void
    {
        if ($sourceFund->getFundCode()->getValue() === 'ZAKAT' && $targetFund->getFundCode()->getValue() === 'GENERAL') {
            throw new BusinessRuleException(
                "BR-FIN-01 Violation: Dana Zakat [ZAKAT] dilarang ditransfer ke Kas Operasional Umum [GENERAL]."
            );
        }

        if ($sourceFund->getFundCode()->getValue() === 'QURBAN' && $targetFund->getFundCode()->getValue() !== 'QURBAN') {
            throw new BusinessRuleException(
                "BR-FIN-03 Violation: Dana Qurban [QURBAN] terisolasi dan dilarang ditransfer ke Fund lain."
            );
        }
    }

    /**
     * BR-FIN-04: Transaksi pengeluaran tidak boleh melebihi saldo kas aktif
     */
    public function validateAccountBalanceForExpense(FinancialAccount $account, Money $expenseAmount): void
    {
        if ($account->getBalance()->getAmount() < $expenseAmount->getAmount()) {
            throw new BusinessRuleException(
                "BR-FIN-04 Violation: Saldo kas [{$account->getCode()}] (Rp {$account->getBalance()->getAmount()}) tidak mencukupi untuk pengeluaran sebesar (Rp {$expenseAmount->getAmount()})."
            );
        }
    }
}
