<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities\ValueObjects;

use App\Domains\Financial\Exceptions\InvalidValueObjectException;

final class ProgramCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));
        if (empty($value) || !preg_match('/^[A-Z0-9_-]{3,50}$/', $value)) {
            throw new InvalidValueObjectException("Kode Program tidak valid: '{$value}'.");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ProgramCode $other): bool
    {
        return $this->value === $other->getValue();
    }
}
