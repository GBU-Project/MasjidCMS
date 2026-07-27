<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class CashBookResponse
{
    public string $masjidId;
    public int $financialAccountId;
    public string $accountCode;
    public string $accountName;
    public float $openingBalance;
    public float $totalInflow;
    public float $totalOutflow;
    public float $endingBalance;
    public array $movements = [];

    public function __construct(
        string $masjidId,
        int $financialAccountId,
        string $accountCode,
        string $accountName,
        float $openingBalance,
        float $totalInflow,
        float $totalOutflow,
        float $endingBalance,
        array $movements = []
    ) {
        $this->masjidId = $masjidId;
        $this->financialAccountId = $financialAccountId;
        $this->accountCode = $accountCode;
        $this->accountName = $accountName;
        $this->openingBalance = round($openingBalance, 2);
        $this->totalInflow = round($totalInflow, 2);
        $this->totalOutflow = round($totalOutflow, 2);
        $this->endingBalance = round($endingBalance, 2);
        $this->movements = $movements;
    }
}
