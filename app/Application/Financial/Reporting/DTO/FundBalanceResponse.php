<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class FundBalanceResponse
{
    public string $masjidId;
    public array $fundBalances = []; // Array of ['fund_id' => int, 'fund_code' => string, 'name' => string, 'type' => string, 'balance' => float]
    public float $totalUnrestrictedBalance;
    public float $totalRestrictedBalance;
    public float $grandTotalBalance;

    public function __construct(
        string $masjidId,
        array $fundBalances,
        float $totalUnrestrictedBalance,
        float $totalRestrictedBalance,
        float $grandTotalBalance
    ) {
        $this->masjidId = $masjidId;
        $this->fundBalances = $fundBalances;
        $this->totalUnrestrictedBalance = round($totalUnrestrictedBalance, 2);
        $this->totalRestrictedBalance = round($totalRestrictedBalance, 2);
        $this->grandTotalBalance = round($grandTotalBalance, 2);
    }
}
