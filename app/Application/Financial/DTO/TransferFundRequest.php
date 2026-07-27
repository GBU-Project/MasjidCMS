<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class TransferFundRequest
{
    public string $masjidId;
    public int $sourceFundId;
    public int $targetFundId;
    public int $sourceFinancialAccountId;
    public int $targetFinancialAccountId;
    public int $transferAccountId; // COA Account
    public string $transactionNo;
    public float $amount;
    public string $transactionDate;
    public ?string $description;
    public ?string $createdBy;

    public function __construct(
        string $masjidId,
        int $sourceFundId,
        int $targetFundId,
        int $sourceFinancialAccountId,
        int $targetFinancialAccountId,
        int $transferAccountId,
        string $transactionNo,
        float $amount,
        string $transactionDate,
        ?string $description = null,
        ?string $createdBy = null
    ) {
        $this->masjidId = $masjidId;
        $this->sourceFundId = $sourceFundId;
        $this->targetFundId = $targetFundId;
        $this->sourceFinancialAccountId = $sourceFinancialAccountId;
        $this->targetFinancialAccountId = $targetFinancialAccountId;
        $this->transferAccountId = $transferAccountId;
        $this->transactionNo = $transactionNo;
        $this->amount = $amount;
        $this->transactionDate = $transactionDate;
        $this->description = $description;
        $this->createdBy = $createdBy;
    }
}
