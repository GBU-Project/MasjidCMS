<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\CoaAccount;
use App\Domains\Financial\Entities\ValueObjects\AccountCode;
use App\Domains\Financial\Repositories\Contracts\CoaAccountRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\CoaAccountDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class CoaAccountRepository implements CoaAccountRepositoryInterface
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

    public function findById(int $id): ?CoaAccount
    {
        $row = $this->db->table('coa_accounts')->where('id', $id)->get()->getRowArray();
        return $row ? CoaAccountDataMapper::toDomain($row) : null;
    }

    public function findByUuid(string $uuid): ?CoaAccount
    {
        $row = $this->db->table('coa_accounts')->where('uuid', $uuid)->get()->getRowArray();
        return $row ? CoaAccountDataMapper::toDomain($row) : null;
    }

    public function findByCode(AccountCode $accountCode): ?CoaAccount
    {
        $row = $this->db->table('coa_accounts')->where('account_code', $accountCode->getValue())->get()->getRowArray();
        return $row ? CoaAccountDataMapper::toDomain($row) : null;
    }

    public function findAllByMasjid(string $masjidId): array
    {
        $rows = $this->db->table('coa_accounts')->where('masjid_id', $masjidId)->get()->getResultArray();
        return array_map([CoaAccountDataMapper::class, 'toDomain'], $rows);
    }

    public function save(CoaAccount $account): CoaAccount
    {
        $data = CoaAccountDataMapper::toDatabaseRow($account);

        if ($account->getId() !== null) {
            $this->db->table('coa_accounts')->where('id', $account->getId())->update($data);
            return $account;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('coa_accounts')->insert($data);
        $insertId = (int) $this->db->insertID();

        $freshRow = $this->db->table('coa_accounts')->where('id', $insertId)->get()->getRowArray();
        return CoaAccountDataMapper::toDomain($freshRow);
    }
}
