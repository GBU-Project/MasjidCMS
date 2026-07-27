<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\ValueObjects\Money;

class FinancialAccountDataMapper
{
    public static function toDomain(array $row): FinancialAccount
    {
        return new FinancialAccount(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['uuid'],
            (string) $row['masjid_id'],
            (string) $row['code'],
            (string) $row['name'],
            $row['account_number'] ?? null,
            $row['bank_name'] ?? null,
            new Money((float) ($row['balance'] ?? 0.00))
        );
    }

    public static function toDatabaseRow(FinancialAccount $account): array
    {
        return [
            'id'             => $account->getId(),
            'uuid'           => $account->getUuid(),
            'masjid_id'      => $account->getMasjidId(),
            'code'           => $account->getCode(),
            'name'           => $account->getName(),
            'account_number' => $account->getAccountNumber(),
            'bank_name'      => $account->getBankName(),
            'balance'        => $account->getBalance()->getAmount(),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }
}
