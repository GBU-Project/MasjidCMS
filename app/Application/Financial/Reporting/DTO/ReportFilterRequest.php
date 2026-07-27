<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\DTO;

class ReportFilterRequest
{
    public string $masjidId;
    public ?string $startDate;
    public ?string $endDate;
    public ?int $fundId;
    public ?int $accountId;
    public ?int $programId;
    public ?string $status;

    public function __construct(
        string $masjidId,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $fundId = null,
        ?int $accountId = null,
        ?int $programId = null,
        ?string $status = null
    ) {
        $this->masjidId = $masjidId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->fundId = $fundId;
        $this->accountId = $accountId;
        $this->programId = $programId;
        $this->status = $status;
    }
}
