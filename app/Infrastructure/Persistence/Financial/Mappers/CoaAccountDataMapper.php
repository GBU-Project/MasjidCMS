<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\CoaAccount;
use App\Domains\Financial\Entities\ValueObjects\AccountCode;

class CoaAccountDataMapper
{
    public static function toDomain(array $row): CoaAccount
    {
        return new CoaAccount(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['uuid'],
            (string) $row['masjid_id'],
            new AccountCode((string) $row['account_code']),
            (string) $row['name'],
            (string) $row['account_type'],
            (bool) ($row['is_active'] ?? true)
        );
    }

    public static function toDatabaseRow(CoaAccount $account): array
    {
        return [
            'id'           => $account->getId(),
            'uuid'         => $account->getUuid(),
            'masjid_id'    => $account->getMasjidId(),
            'account_code' => $account->getAccountCode()->getValue(),
            'name'         => $account->getName(),
            'account_type' => $account->getAccountType(),
            'is_active'    => $account->isActive() ? 1 : 0,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];
    }
}
