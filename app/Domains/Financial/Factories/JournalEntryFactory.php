<?php

declare(strict_types=1);

namespace App\Domains\Financial\Factories;

use App\Domains\Financial\Entities\JournalDetail;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;

class JournalEntryFactory
{
    public static function createSimpleJournal(
        string $uuid,
        int $transactionId,
        JournalNumber $journalNo,
        string $entryDate,
        int $debitAccountId,
        int $creditAccountId,
        Money $amount,
        ?string $description = null
    ): JournalEntry {
        $debitLine = new JournalDetail(null, null, $debitAccountId, $amount, new Money(0.00));
        $creditLine = new JournalDetail(null, null, $creditAccountId, new Money(0.00), $amount);

        $journal = new JournalEntry(
            null,
            $uuid,
            $transactionId,
            $journalNo,
            $entryDate,
            $description,
            [$debitLine, $creditLine]
        );

        $journal->assertBalanced();

        return $journal;
    }
}
