<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;

class FundDataMapper
{
    public static function toDomain(array $row): Fund
    {
        return new Fund(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['uuid'],
            (string) $row['masjid_id'],
            new FundCode((string) $row['fund_code']),
            (string) $row['name'],
            (string) ($row['fund_type'] ?? 'UNRESTRICTED'),
            (string) ($row['status'] ?? 'ACTIVE'),
            $row['created_at'] ?? null,
            $row['updated_at'] ?? null,
            $row['deleted_at'] ?? null
        );
    }

    public static function toDatabaseRow(Fund $fund): array
    {
        return [
            'id'         => $fund->getId(),
            'uuid'       => $fund->getUuid(),
            'masjid_id'  => $fund->getMasjidId(),
            'fund_code'  => $fund->getFundCode()->getValue(),
            'name'       => $fund->getName(),
            'fund_type'  => $fund->getFundType(),
            'status'     => $fund->getStatus(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }
}
