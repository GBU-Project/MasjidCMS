<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

use App\Domains\Financial\Entities\FinancialTransaction;

class FinancialTransactionResponse
{
    public ?int $id;
    public string $uuid;
    public string $masjidId;
    public int $fundId;
    public int $accountId;
    public int $financialAccountId;
    public string $transactionNo;
    public string $transactionType;
    public float $amount;
    public string $status;
    public string $transactionDate;
    public ?string $description;
    public ?string $createdBy;
    public ?string $approvedBy;
    public ?string $postedAt;

    public static function fromEntity(FinancialTransaction $trx): self
    {
        $response = new self();
        $response->id = $trx->getId();
        $response->uuid = $trx->getUuid();
        $response->masjidId = $trx->getMasjidId();
        $response->fundId = $trx->getFundId();
        $response->accountId = $trx->getAccountId();
        $response->financialAccountId = $trx->getFinancialAccountId();
        $response->transactionNo = $trx->getTransactionNo()->getValue();
        $response->transactionType = $trx->getTransactionType();
        $response->amount = $trx->getAmount()->getAmount();
        $response->status = $trx->getStatus();
        $response->transactionDate = $trx->getTransactionDate();
        $response->description = $trx->getDescription();
        $response->createdBy = $trx->getCreatedBy();
        $response->approvedBy = $trx->getApprovedBy();
        $response->postedAt = $trx->getPostedAt();
        return $response;
    }
}
