<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\AccountCode;

class CoaAccount
{
    private ?int $id;
    private string $uuid;
    private string $masjidId;
    private AccountCode $accountCode;
    private string $name;
    private string $accountType; // 'ASSET', 'LIABILITY', 'FUND_BALANCE', 'INCOME', 'EXPENSE'
    private bool $isActive;

    public function __construct(
        ?int $id,
        string $uuid,
        string $masjidId,
        AccountCode $accountCode,
        string $name,
        string $accountType,
        bool $isActive = true
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->masjidId = $masjidId;
        $this->accountCode = $accountCode;
        $this->name = trim($name);
        $this->accountType = strtoupper($accountType);
        $this->isActive = $isActive;
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

    public function getAccountCode(): AccountCode
    {
        return $this->accountCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function equals(CoaAccount $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
