<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Repositories;

use App\Domains\Financial\Entities\Program;
use App\Domains\Financial\Entities\ValueObjects\ProgramCode;
use App\Domains\Financial\Repositories\Contracts\ProgramRepositoryInterface;
use App\Infrastructure\Persistence\Financial\Mappers\ProgramDataMapper;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class ProgramRepository implements ProgramRepositoryInterface
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

    public function findById(int $id): ?Program
    {
        $row = $this->db->table('programs')->where('id', $id)->get()->getRowArray();
        return $row ? ProgramDataMapper::toDomain($row) : null;
    }

    public function findByUuid(string $uuid): ?Program
    {
        $row = $this->db->table('programs')->where('uuid', $uuid)->get()->getRowArray();
        return $row ? ProgramDataMapper::toDomain($row) : null;
    }

    public function findByCode(ProgramCode $programCode): ?Program
    {
        $row = $this->db->table('programs')->where('program_code', $programCode->getValue())->get()->getRowArray();
        return $row ? ProgramDataMapper::toDomain($row) : null;
    }

    public function save(Program $program): Program
    {
        $data = ProgramDataMapper::toDatabaseRow($program);

        if ($program->getId() !== null) {
            $this->db->table('programs')->where('id', $program->getId())->update($data);
            return $program;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('programs')->insert($data);
        $insertId = (int) $this->db->insertID();

        $freshRow = $this->db->table('programs')->where('id', $insertId)->get()->getRowArray();
        return ProgramDataMapper::toDomain($freshRow);
    }
}
