<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class GeneralLedgerResponse
{
    public string $masjidId;
    public int $accountId;
    public string $accountCode;
    public string $accountName;
    public float $openingBalance;
    public array $entries = []; // Array of ['date' => string, 'journal_no' => string, 'description' => string, 'debit' => float, 'credit' => float, 'running_balance' => float]
    public float $closingBalance;

    public function __construct(
        string $masjidId,
        int $accountId,
        string $accountCode,
        string $accountName,
        float $openingBalance,
        array $entries,
        float $closingBalance
    ) {
        $this->masjidId = $masjidId;
        $this->accountId = $accountId;
        $this->accountCode = $accountCode;
        $this->accountName = $accountName;
        $this->openingBalance = round($openingBalance, 2);
        $this->entries = $entries;
        $this->closingBalance = round($closingBalance, 2);
    }
}
