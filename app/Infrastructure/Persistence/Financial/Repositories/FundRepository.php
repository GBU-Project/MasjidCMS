<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\FundDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class FundRepository implements FundRepositoryInterface
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

    public function findById(int $id): ?Fund
    {
        if ($this->db === null) {
            return null;
        }
        $row = $this->db->table('funds')->where('id', $id)->where('deleted_at', null)->get()->getRowArray();
        return $row ? FundDataMapper::toDomain($row) : null;
    }

    public function findByIdForUpdate(int $id): ?Fund
    {
        if ($this->db === null) {
            return null;
        }
        $row = $this->db->table('funds')->where('id', $id)->where('deleted_at', null)->forUpdate()->get()->getRowArray();
        return $row ? FundDataMapper::toDomain($row) : null;
    }

    public function findByUuid(string $uuid): ?Fund
    {
        $row = $this->db->table('funds')->where('uuid', $uuid)->where('deleted_at', null)->get()->getRowArray();
        return $row ? FundDataMapper::toDomain($row) : null;
    }

    public function findByCode(FundCode $fundCode): ?Fund
    {
        $row = $this->db->table('funds')->where('fund_code', $fundCode->getValue())->where('deleted_at', null)->get()->getRowArray();
        return $row ? FundDataMapper::toDomain($row) : null;
    }

    public function findAllByMasjid(string $masjidId): array
    {
        $rows = $this->db->table('funds')->where('masjid_id', $masjidId)->where('deleted_at', null)->get()->getResultArray();
        return array_map([FundDataMapper::class, 'toDomain'], $rows);
    }

    public function save(Fund $fund): Fund
    {
        $data = FundDataMapper::toDatabaseRow($fund);

        if ($fund->getId() !== null) {
            $this->db->table('funds')->where('id', $fund->getId())->update($data);
            return $fund;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('funds')->insert($data);
        $insertId = (int) $this->db->insertID();

        $freshRow = $this->db->table('funds')->where('id', $insertId)->get()->getRowArray();
        return FundDataMapper::toDomain($freshRow);
    }

    public function delete(Fund $fund): bool
    {
        if ($fund->getId() === null) {
            return false;
        }
        return $this->db->table('funds')->where('id', $fund->getId())->update(['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
