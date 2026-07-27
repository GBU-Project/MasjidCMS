<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class CreateTransactionRequest
{
    public string $masjidId;
    public int $fundId;
    public int $accountId;
    public int $financialAccountId;
    public string $transactionNo;
    public string $transactionType; // 'INCOME', 'EXPENSE', 'TRANSFER', 'ADJUSTMENT'
    public float $amount;
    public string $transactionDate;
    public ?int $programId;
    public ?string $jamaahId;
    public ?string $familyId;
    public ?string $vendorId;
    public ?string $assetId;
    public string $paymentMethod;
    public ?string $description;
    public ?string $createdBy;

    public function __construct(
        string $masjidId,
        int $fundId,
        int $accountId,
        int $financialAccountId,
        string $transactionNo,
        string $transactionType,
        float $amount,
        string $transactionDate,
        ?int $programId = null,
        ?string $jamaahId = null,
        ?string $familyId = null,
        ?string $vendorId = null,
        ?string $assetId = null,
        string $paymentMethod = 'CASH',
        ?string $description = null,
        ?string $createdBy = null
    ) {
        $this->masjidId = $masjidId;
        $this->fundId = $fundId;
        $this->accountId = $accountId;
        $this->financialAccountId = $financialAccountId;
        $this->transactionNo = $transactionNo;
        $this->transactionType = $transactionType;
        $this->amount = $amount;
        $this->transactionDate = $transactionDate;
        $this->programId = $programId;
        $this->jamaahId = $jamaahId;
        $this->familyId = $familyId;
        $this->vendorId = $vendorId;
        $this->assetId = $assetId;
        $this->paymentMethod = $paymentMethod;
        $this->description = $description;
        $this->createdBy = $createdBy;
    }
}
