<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;

interface JournalEntryRepositoryInterface
{
    public function findById(int $id): ?JournalEntry;
    public function findByUuid(string $uuid): ?JournalEntry;
    public function findByJournalNo(JournalNumber $journalNo): ?JournalEntry;
    public function findByTransactionId(int $transactionId): ?JournalEntry;
    public function save(JournalEntry $journal): JournalEntry;
}
