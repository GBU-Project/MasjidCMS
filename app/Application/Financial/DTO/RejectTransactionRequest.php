<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class RejectTransactionRequest
{
    public string $transactionUuid;
    public string $approverUserId;
    public ?string $notes;

    public function __construct(string $transactionUuid, string $approverUserId, ?string $notes = null)
    {
        $this->transactionUuid = $transactionUuid;
        $this->approverUserId = $approverUserId;
        $this->notes = $notes;
    }
}
