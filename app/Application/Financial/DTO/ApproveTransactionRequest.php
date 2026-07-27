<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class ApproveTransactionRequest
{
    public string $transactionUuid;
    public string $approverUserId;

    public function __construct(string $transactionUuid, string $approverUserId)
    {
        $this->transactionUuid = $transactionUuid;
        $this->approverUserId = $approverUserId;
    }
}
