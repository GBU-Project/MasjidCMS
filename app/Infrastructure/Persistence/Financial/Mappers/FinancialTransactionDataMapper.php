<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\Mappers;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;

class FinancialTransactionDataMapper
{
    public static function toDomain(array $row): FinancialTransaction
    {
        return new FinancialTransaction(
            isset($row['id']) ? (int) $row['id'] : null,
            (string) $row['uuid'],
            (string) $row['masjid_id'],
            (int) $row['fund_id'],
            (int) $row['account_id'],
            (int) $row['financial_account_id'],
            new TransactionNumber((string) $row['transaction_no']),
            (string) $row['transaction_type'],
            new Money((float) $row['amount']),
            (string) $row['transaction_date'],
            isset($row['program_id']) ? (int) $row['program_id'] : null,
            $row['jamaah_id'] ?? null,
            $row['family_id'] ?? null,
            $row['vendor_id'] ?? null,
            $row['asset_id'] ?? null,
            (string) ($row['payment_method'] ?? 'CASH'),
            (string) ($row['status'] ?? 'DRAFT'),
            $row['description'] ?? null,
            $row['created_by'] ?? null,
            $row['updated_by'] ?? null,
            $row['approved_by'] ?? null,
            $row['posted_at'] ?? null
        );
    }

    public static function toDatabaseRow(FinancialTransaction $trx): array
    {
        return [
            'id'                   => $trx->getId(),
            'uuid'                 => $trx->getUuid(),
            'masjid_id'            => $trx->getMasjidId(),
            'fund_id'              => $trx->getFundId(),
            'account_id'           => $trx->getAccountId(),
            'financial_account_id' => $trx->getFinancialAccountId(),
            'program_id'           => $trx->getProgramId(),
            'jamaah_id'            => $trx->getJamaahId(),
            'family_id'            => $trx->getFamilyId(),
            'vendor_id'            => $trx->getVendorId(),
            'asset_id'             => $trx->getAssetId(),
            'transaction_no'       => $trx->getTransactionNo()->getValue(),
            'transaction_type'     => $trx->getTransactionType(),
            'amount'               => $trx->getAmount()->getAmount(),
            'payment_method'       => $trx->getPaymentMethod(),
            'status'               => $trx->getStatus(),
            'transaction_date'     => $trx->getTransactionDate(),
            'description'          => $trx->getDescription(),
            'created_by'           => $trx->getCreatedBy(),
            'approved_by'          => $trx->getApprovedBy(),
            'posted_at'            => $trx->getPostedAt(),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];
    }
}
