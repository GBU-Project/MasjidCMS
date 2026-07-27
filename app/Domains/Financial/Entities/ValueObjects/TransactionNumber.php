<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities\ValueObjects;

use App\Domains\Financial\Exceptions\InvalidValueObjectException;

final class TransactionNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (!preg_match('/^TRX-\d{6}-\d{5}$/', $value)) {
            throw new InvalidValueObjectException("Format Nomor Transaksi tidak valid: '{$value}'. Format harus 'TRX-YYYYMM-XXXXX'.");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(TransactionNumber $other): bool
    {
        return $this->value === $other->getValue();
    }
}
