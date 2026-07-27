<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities\ValueObjects;

use App\Domains\Financial\Exceptions\InvalidValueObjectException;

final class AccountCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        if (!preg_match('/^\d{5}$/', $value)) {
            throw new InvalidValueObjectException("Kode Akun COA harus berupa 5 digit angka: '{$value}'.");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(AccountCode $other): bool
    {
        return $this->value === $other->getValue();
    }
}
