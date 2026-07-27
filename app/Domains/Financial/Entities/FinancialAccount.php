<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class FinancialAccount
{
    private ?int $id;
    private string $uuid;
    private string $masjidId;
    private string $code;
    private string $name;
    private ?string $accountNumber;
    private ?string $bankName;
    private Money $balance;

    public function __construct(
        ?int $id,
        string $uuid,
        string $masjidId,
        string $code,
        string $name,
        ?string $accountNumber = null,
        ?string $bankName = null,
        ?Money $balance = null
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->masjidId = $masjidId;
        $this->code = strtoupper(trim($code));
        $this->name = trim($name);
        $this->accountNumber = $accountNumber;
        $this->bankName = $bankName;
        $this->balance = $balance ?? new Money(0.00);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getMasjidId(): string
    {
        return $this->masjidId;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function credit(Money $amount): void
    {
        $this->balance = $this->balance->add($amount);
    }

    public function debit(Money $amount): void
    {
        if ($this->balance->getAmount() < $amount->getAmount()) {
            throw new BusinessRuleException("Saldo kas [{$this->code}] tidak mencukupi untuk pengeluaran.");
        }
        $this->balance = $this->balance->subtract($amount);
    }

    public function equals(FinancialAccount $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
