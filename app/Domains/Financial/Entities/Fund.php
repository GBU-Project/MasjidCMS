<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Events\HasDomainEventsTrait;
use App\Domains\Financial\Exceptions\BusinessRuleException;

class Fund
{
    use HasDomainEventsTrait;
    private ?int $id;
    private string $uuid;
    private string $masjidId;
    private FundCode $fundCode;
    private string $name;
    private string $fundType; // 'UNRESTRICTED', 'RESTRICTED', 'ENDOWMENT'
    private string $status;   // 'ACTIVE', 'INACTIVE'
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?string $deletedAt;

    public function __construct(
        ?int $id,
        string $uuid,
        string $masjidId,
        FundCode $fundCode,
        string $name,
        string $fundType = 'UNRESTRICTED',
        string $status = 'ACTIVE',
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $deletedAt = null
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->masjidId = $masjidId;
        $this->fundCode = $fundCode;
        $this->name = trim($name);
        $this->fundType = strtoupper($fundType);
        $this->status = strtoupper($status);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
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

    public function getFundCode(): FundCode
    {
        return $this->fundCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFundType(): string
    {
        return $this->fundType;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isRestricted(): bool
    {
        return $this->fundType === 'RESTRICTED';
    }

    public function deactivate(): void
    {
        if ($this->status === 'INACTIVE') {
            throw new BusinessRuleException("Fund [{$this->fundCode->getValue()}] sudah berstatus INACTIVE.");
        }
        $this->status = 'INACTIVE';
    }

    public function activate(): void
    {
        $this->status = 'ACTIVE';
    }

    public function equals(Fund $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
