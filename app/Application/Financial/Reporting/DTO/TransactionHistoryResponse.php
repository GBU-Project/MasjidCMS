<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class TransactionHistoryResponse
{
    public string $masjidId;
    public int $totalCount;
    public array $transactions = [];

    public function __construct(string $masjidId, array $transactions)
    {
        $this->masjidId = $masjidId;
        $this->transactions = $transactions;
        $this->totalCount = count($transactions);
    }
}
