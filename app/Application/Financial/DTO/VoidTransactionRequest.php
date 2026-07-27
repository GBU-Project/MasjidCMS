<?php

declare(strict_types=1);

namespace App\Application\Financial\DTO;

class VoidTransactionRequest
{
    public string $transactionUuid;
    public string $reversalJournalNo;

    public function __construct(string $transactionUuid, string $reversalJournalNo)
    {
        $this->transactionUuid = $transactionUuid;
        $this->reversalJournalNo = $reversalJournalNo;
    }
}
