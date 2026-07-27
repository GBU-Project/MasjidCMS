<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\JournalEntryDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class JournalEntryRepository implements JournalEntryRepositoryInterface
{
    protected ?BaseConnection $db = null;

    public function __construct(?BaseConnection $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            try {
                $this->db = Database::connect();
            } catch (\Throwable $e) {
                $this->db = null;
            }
        }
    }

    public function findById(int $id): ?JournalEntry
    {
        $header = $this->db->table('journal_entries')->where('id', $id)->get()->getRowArray();
        if (!$header) {
            return null;
        }

        $details = $this->db->table('journal_details')->where('journal_id', $id)->get()->getResultArray();
        return JournalEntryDataMapper::toDomain($header, $details);
    }

    public function findByUuid(string $uuid): ?JournalEntry
    {
        $header = $this->db->table('journal_entries')->where('uuid', $uuid)->get()->getRowArray();
        if (!$header) {
            return null;
        }

        $details = $this->db->table('journal_details')->where('journal_id', (int) $header['id'])->get()->getResultArray();
        return JournalEntryDataMapper::toDomain($header, $details);
    }

    public function findByJournalNo(JournalNumber $journalNo): ?JournalEntry
    {
        $header = $this->db->table('journal_entries')->where('journal_no', $journalNo->getValue())->get()->getRowArray();
        if (!$header) {
            return null;
        }

        $details = $this->db->table('journal_details')->where('journal_id', (int) $header['id'])->get()->getResultArray();
        return JournalEntryDataMapper::toDomain($header, $details);
    }

    public function findByTransactionId(int $transactionId): ?JournalEntry
    {
        $header = $this->db->table('journal_entries')->where('transaction_id', $transactionId)->get()->getRowArray();
        if (!$header) {
            return null;
        }

        $details = $this->db->table('journal_details')->where('journal_id', (int) $header['id'])->get()->getResultArray();
        return JournalEntryDataMapper::toDomain($header, $details);
    }

    public function save(JournalEntry $journal): JournalEntry
    {
        $headerData = JournalEntryDataMapper::toDatabaseRow($journal);

        if ($journal->getId() !== null) {
            $journalId = $journal->getId();
            $this->db->table('journal_entries')->where('id', $journalId)->update($headerData);
            // Replace details
            $this->db->table('journal_details')->where('journal_id', $journalId)->delete();
        } else {
            $headerData['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('journal_entries')->insert($headerData);
            $journalId = (int) $this->db->insertID();
        }

        foreach ($journal->getDetails() as $detail) {
            $detailRow = JournalEntryDataMapper::detailToDatabaseRow($journalId, $detail);
            $this->db->table('journal_details')->insert($detailRow);
        }

        return $this->findById($journalId);
    }
}
