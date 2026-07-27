<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\FinancialAccountDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class FinancialAccountRepository implements FinancialAccountRepositoryInterface
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

    public function findById(int $id): ?FinancialAccount
    {
        if ($this->db === null) {
            return null;
        }
        $row = $this->db->table('financial_accounts')->where('id', $id)->get()->getRowArray();
        return $row ? FinancialAccountDataMapper::toDomain($row) : null;
    }

    public function findByIdForUpdate(int $id): ?FinancialAccount
    {
        if ($this->db === null) {
            return null;
        }
        $row = $this->db->table('financial_accounts')->where('id', $id)->forUpdate()->get()->getRowArray();
        return $row ? FinancialAccountDataMapper::toDomain($row) : null;
    }

    public function findByUuid(string $uuid): ?FinancialAccount
    {
        $row = $this->db->table('financial_accounts')->where('uuid', $uuid)->get()->getRowArray();
        return $row ? FinancialAccountDataMapper::toDomain($row) : null;
    }

    public function findByCode(string $code): ?FinancialAccount
    {
        $row = $this->db->table('financial_accounts')->where('code', strtoupper($code))->get()->getRowArray();
        return $row ? FinancialAccountDataMapper::toDomain($row) : null;
    }

    public function save(FinancialAccount $account): FinancialAccount
    {
        $data = FinancialAccountDataMapper::toDatabaseRow($account);

        if ($account->getId() !== null) {
            $this->db->table('financial_accounts')->where('id', $account->getId())->update($data);
            return $account;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('financial_accounts')->insert($data);
        $insertId = (int) $this->db->insertID();

        $freshRow = $this->db->table('financial_accounts')->where('id', $insertId)->get()->getRowArray();
        return FinancialAccountDataMapper::toDomain($freshRow);
    }
}
