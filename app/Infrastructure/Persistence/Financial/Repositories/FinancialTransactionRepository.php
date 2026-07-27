<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\FinancialTransactionDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class FinancialTransactionRepository implements FinancialTransactionRepositoryInterface
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

    public function findById(int $id): ?FinancialTransaction
    {
        $row = $this->db->table('financial_transactions')->where('id', $id)->where('deleted_at', null)->get()->getRowArray();
        return $row ? FinancialTransactionDataMapper::toDomain($row) : null;
    }

    public function findByUuid(string $uuid): ?FinancialTransaction
    {
        $row = $this->db->table('financial_transactions')->where('uuid', $uuid)->where('deleted_at', null)->get()->getRowArray();
        return $row ? FinancialTransactionDataMapper::toDomain($row) : null;
    }

    public function findByTransactionNo(TransactionNumber $transactionNo): ?FinancialTransaction
    {
        $row = $this->db->table('financial_transactions')->where('transaction_no', $transactionNo->getValue())->where('deleted_at', null)->get()->getRowArray();
        return $row ? FinancialTransactionDataMapper::toDomain($row) : null;
    }

    public function save(FinancialTransaction $transaction): FinancialTransaction
    {
        $data = FinancialTransactionDataMapper::toDatabaseRow($transaction);

        if ($transaction->getId() !== null) {
            $this->db->table('financial_transactions')->where('id', $transaction->getId())->update($data);
            return $transaction;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('financial_transactions')->insert($data);
        $insertId = (int) $this->db->insertID();

        $freshRow = $this->db->table('financial_transactions')->where('id', $insertId)->get()->getRowArray();
        return FinancialTransactionDataMapper::toDomain($freshRow);
    }
}
