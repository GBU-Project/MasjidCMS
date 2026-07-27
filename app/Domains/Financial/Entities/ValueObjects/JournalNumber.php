<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities\ValueObjects;

use App\Domains\Financial\Exceptions\InvalidValueObjectException;

final class JournalNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (!preg_match('/^JRN-\d{6}-\d{5}$/', $value)) {
            throw new InvalidValueObjectException("Format Nomor Jurnal tidak valid: '{$value}'. Format harus 'JRN-YYYYMM-XXXXX'.");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(JournalNumber $other): bool
    {
        return $this->value === $other->getValue();
    }
}
