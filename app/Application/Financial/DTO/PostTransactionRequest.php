<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class PostTransactionRequest
{
    public string $transactionUuid;
    public string $journalNo;
    public int $cashAccountId;

    public function __construct(string $transactionUuid, string $journalNo, int $cashAccountId)
    {
        $this->transactionUuid = $transactionUuid;
        $this->journalNo = $journalNo;
        $this->cashAccountId = $cashAccountId;
    }
}
