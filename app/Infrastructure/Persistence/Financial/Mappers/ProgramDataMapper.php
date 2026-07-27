<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\Program;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\ProgramCode;

class ProgramDataMapper
{
    public static function toDomain(array $row): Program
    {
        return new Program(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['uuid'],
            (string) $row['masjid_id'],
            (int) $row['fund_id'],
            new ProgramCode((string) $row['program_code']),
            (string) $row['name'],
            isset($row['target_amount']) ? new Money((float) $row['target_amount']) : null,
            (string) ($row['status'] ?? 'ACTIVE')
        );
    }

    public static function toDatabaseRow(Program $program): array
    {
        return [
            'id'            => $program->getId(),
            'uuid'          => $program->getUuid(),
            'masjid_id'     => $program->getMasjidId(),
            'fund_id'       => $program->getFundId(),
            'program_code'  => $program->getProgramCode()->getValue(),
            'name'          => $program->getName(),
            'target_amount' => $program->getTargetAmount() ? $program->getTargetAmount()->getAmount() : null,
            'status'        => $program->getStatus(),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
    }
}
