<?php

declare(strict_types=1);

namespace App\Domains\Financial\Entities;

use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\ProgramCode;

class Program
{
    private ?int $id;
    private string $uuid;
    private string $masjidId;
    private int $fundId;
    private ProgramCode $programCode;
    private string $name;
    private ?Money $targetAmount;
    private string $status; // 'ACTIVE', 'COMPLETED'

    public function __construct(
        ?int $id,
        string $uuid,
        string $masjidId,
        int $fundId,
        ProgramCode $programCode,
        string $name,
        ?Money $targetAmount = null,
        string $status = 'ACTIVE'
    ) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->masjidId = $masjidId;
        $this->fundId = $fundId;
        $this->programCode = $programCode;
        $this->name = trim($name);
        $this->targetAmount = $targetAmount;
        $this->status = strtoupper($status);
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

    public function getFundId(): int
    {
        return $this->fundId;
    }

    public function getProgramCode(): ProgramCode
    {
        return $this->programCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargetAmount(): ?Money
    {
        return $this->targetAmount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function equals(Program $other): bool
    {
        return $this->uuid === $other->getUuid();
    }
}
