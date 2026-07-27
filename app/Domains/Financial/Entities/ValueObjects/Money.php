<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities\ValueObjects;

use App\Domains\Financial\Exceptions\InvalidValueObjectException;

final class Money
{
    private float $amount;
    private string $currency;

    public function __construct(float $amount, string $currency = 'IDR')
    {
        if ($amount < 0) {
            throw new InvalidValueObjectException("Nominal uang tidak boleh bernilai negatif: {$amount}");
        }
        $this->amount = round($amount, 2);
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->getAmount() && $this->currency === $other->getCurrency();
    }

    public function add(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidValueObjectException("Mata uang tidak cocok untuk penjumlahan.");
        }
        return new self($this->amount + $other->getAmount(), $this->currency);
    }

    public function subtract(Money $other): Money
    {
        if ($this->currency !== $other->getCurrency()) {
            throw new InvalidValueObjectException("Mata uang tidak cocok untuk pengurangan.");
        }
        return new self($this->amount - $other->getAmount(), $this->currency);
    }
}
