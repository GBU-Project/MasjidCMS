<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\Money;

class JournalDetail
{
    private ?int $id;
    private ?int $journalId;
    private int $accountId;
    private Money $debitAmount;
    private Money $creditAmount;

    public function __construct(
        ?int $id,
        ?int $journalId,
        int $accountId,
        Money $debitAmount,
        Money $creditAmount
    ) {
        $this->id = $id;
        $this->journalId = $journalId;
        $this->accountId = $accountId;
        $this->debitAmount = $debitAmount;
        $this->creditAmount = $creditAmount;
    }

    public function getId(): ?int { return $this->id; }
    public function getJournalId(): ?int { return $this->journalId; }
    public function getAccountId(): int { return $this->accountId; }
    public function getDebitAmount(): Money { return $this->debitAmount; }
    public function getCreditAmount(): Money { return $this->creditAmount; }
}
