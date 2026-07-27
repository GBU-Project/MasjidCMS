<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class TrialBalanceReportResponse
{
    public string $masjidId;
    public string $period;
    public array $accountBalances = []; // Array of ['account_code' => string, 'name' => string, 'debit' => float, 'credit' => float]
    public float $totalDebit = 0.0;
    public float $totalCredit = 0.0;
    public bool $isBalanced = true;

    public function __construct(
        string $masjidId,
        string $period,
        array $accountBalances,
        float $totalDebit,
        float $totalCredit
    ) {
        $this->masjidId = $masjidId;
        $this->period = $period;
        $this->accountBalances = $accountBalances;
        $this->totalDebit = round($totalDebit, 2);
        $this->totalCredit = round($totalCredit, 2);
        $this->isBalanced = (abs($this->totalDebit - $this->totalCredit) < 0.01);
    }
}
