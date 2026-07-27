<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Posting;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\JournalDetail;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Factories\JournalEntryFactory;

class JournalBuilder
{
    public function buildFromTransaction(
        FinancialTransaction $transaction,
        JournalNumber $journalNo,
        int $cashAccountId
    ): JournalEntry {
        $amount = $transaction->getAmount();
        $date = $transaction->getTransactionDate();

        switch ($transaction->getTransactionType()) {
            case 'INCOME':
                // Debit: Kas/Bank Account | Credit: COA Income Account
                return JournalEntryFactory::createSimpleJournal(
                    'jrn-' . bin2hex(random_bytes(8)),
                    (int) $transaction->getId(),
                    $journalNo,
                    $date,
                    $cashAccountId,
                    $transaction->getAccountId(),
                    $amount,
                    $transaction->getDescription() ?? "Journal Posting for {$transaction->getTransactionNo()->getValue()}"
                );

            case 'EXPENSE':
                // Debit: COA Expense Account | Credit: Kas/Bank Account
                return JournalEntryFactory::createSimpleJournal(
                    'jrn-' . bin2hex(random_bytes(8)),
                    (int) $transaction->getId(),
                    $journalNo,
                    $date,
                    $transaction->getAccountId(),
                    $cashAccountId,
                    $amount,
                    $transaction->getDescription() ?? "Journal Posting for {$transaction->getTransactionNo()->getValue()}"
                );

            case 'TRANSFER':
            case 'ADJUSTMENT':
                // Custom double-entry mapping
                return JournalEntryFactory::createSimpleJournal(
                    'jrn-' . bin2hex(random_bytes(8)),
                    (int) $transaction->getId(),
                    $journalNo,
                    $date,
                    $cashAccountId,
                    $transaction->getAccountId(),
                    $amount,
                    $transaction->getDescription() ?? "Journal Posting for {$transaction->getTransactionNo()->getValue()}"
                );

            default:
                throw new BusinessRuleException("Tipe transaksi [{$transaction->getTransactionType()}] tidak dikenali oleh JournalBuilder.");
        }
    }

    public function buildReversalJournal(
        JournalEntry $originalJournal,
        JournalNumber $reversalJournalNo,
        string $reversalDate
    ): JournalEntry {
        $reversalDetails = [];
        foreach ($originalJournal->getDetails() as $line) {
            // Swap Debit & Credit
            $reversalDetails[] = new JournalDetail(
                null,
                null,
                $line->getAccountId(),
                $line->getCreditAmount(),
                $line->getDebitAmount()
            );
        }

        $reversalJournal = new JournalEntry(
            null,
            'jrn-' . bin2hex(random_bytes(8)),
            $originalJournal->getTransactionId(),
            $reversalJournalNo,
            $reversalDate,
            "Reversal of Journal {$originalJournal->getJournalNo()->getValue()}",
            $reversalDetails
        );

        $reversalJournal->assertBalanced();

        return $reversalJournal;
    }
}
