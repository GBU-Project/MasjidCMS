<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Events\HasDomainEventsTrait;
use App\Domains\Financial\Events\JournalEntryCreatedEvent;
use App\Domains\Financial\Events\JournalPostedEvent;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class JournalEntry
{
    use HasDomainEventsTrait;

    private ?int $id;
    private string $uuid;
    private int $transactionId;
    private JournalNumber $journalNo;
    private string $entryDate;
    private ?string $description;
    /** @var JournalDetail[] */
    private array $details = [];

    public function __construct(
        ?int $id,
        string $uuid,
        int $transactionId,
        JournalNumber $journalNo,
        string $entryDate,
        ?string $description = null,
        array $details = []
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->transactionId = $transactionId;
        $this->journalNo = $journalNo;
        $this->entryDate = $entryDate;
        $this->description = $description;
        foreach ($details as $detail) {
            $this->addDetail($detail);
        }

        $this->recordEvent(new JournalEntryCreatedEvent('evt-' . bin2hex(random_bytes(4)), $this->uuid, [
            'journal_no'     => $this->journalNo->getValue(),
            'transaction_id' => $this->transactionId,
        ]));
    }

    public function getId(): ?int { return $this->id; }
    public function getUuid(): string { return $this->uuid; }
    public function getTransactionId(): int { return $this->transactionId; }
    public function getJournalNo(): JournalNumber { return $this->journalNo; }
    public function getEntryDate(): string { return $this->entryDate; }
    public function getDescription(): ?string { return $this->description; }
    /** @return JournalDetail[] */
    public function getDetails(): array { return $this->details; }

    public function addDetail(JournalDetail $detail): void
    {
        $this->details[] = $detail;
    }

    public function getTotalDebit(): float
    {
        $total = 0.0;
        foreach ($this->details as $detail) {
            $total += $detail->getDebitAmount()->getAmount();
        }
        return round($total, 2);
    }

    public function getTotalCredit(): float
    {
        $total = 0.0;
        foreach ($this->details as $detail) {
            $total += $detail->getCreditAmount()->getAmount();
        }
        return round($total, 2);
    }

    public function isBalanced(): bool
    {
        return $this->getTotalDebit() === $this->getTotalCredit() && count($this->details) >= 2;
    }

    public function assertBalanced(): void
    {
        if (!$this->isBalanced()) {
            throw new BusinessRuleException(
                "Entri Jurnal [{$this->journalNo->getValue()}] tidak seimbang! Total Debit (Rp {$this->getTotalDebit()}) != Total Kredit (Rp {$this->getTotalCredit()})."
            );
        }
    }

    public function equals(JournalEntry $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
