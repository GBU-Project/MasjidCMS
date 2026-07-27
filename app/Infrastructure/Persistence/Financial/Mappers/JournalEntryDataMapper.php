<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\JournalDetail;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;

class JournalEntryDataMapper
{
    public static function toDomain(array $headerRow, array $detailRows = []): JournalEntry
    {
        $details = [];
        foreach ($detailRows as $row) {
            $details[] = new JournalDetail(
                isset($row['id']) ? (int) $row['id'] : null,
                isset($row['journal_id']) ? (int) $row['journal_id'] : null,
                (int) $row['account_id'],
                new Money((float) ($row['debit_amount'] ?? 0.00)),
                new Money((float) ($row['credit_amount'] ?? 0.00))
            );
        }

        return new JournalEntry(
            isset($headerRow['id']) ? (int) $headerRow['id'] : null,
            (string) $headerRow['uuid'],
            (int) $headerRow['transaction_id'],
            new JournalNumber((string) $headerRow['journal_no']),
            (string) $headerRow['entry_date'],
            $headerRow['description'] ?? null,
            $details
        );
    }

    public static function toDatabaseRow(JournalEntry $journal): array
    {
        return [
            'id'             => $journal->getId(),
            'uuid'           => $journal->getUuid(),
            'transaction_id' => $journal->getTransactionId(),
            'journal_no'     => $journal->getJournalNo()->getValue(),
            'entry_date'     => $journal->getEntryDate(),
            'description'    => $journal->getDescription(),
        ];
    }

    public static function detailToDatabaseRow(int $journalId, JournalDetail $detail): array
    {
        return [
            'id'            => $detail->getId(),
            'journal_id'    => $journalId,
            'account_id'    => $detail->getAccountId(),
            'debit_amount'  => $detail->getDebitAmount()->getAmount(),
            'credit_amount' => $detail->getCreditAmount()->getAmount(),
        ];
    }
}
