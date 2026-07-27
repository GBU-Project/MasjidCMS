<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\Services;

use App\Application\Financial\Reporting\DTO\CashBookResponse;
use App\Application\Financial\Reporting\DTO\ReportFilterRequest;
use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;

class CashBookReportService
{
    private FinancialAccountRepositoryInterface $finAccountRepo;

    public function __construct(FinancialAccountRepositoryInterface $finAccountRepo)
    {
        $this->finAccountRepo = $finAccountRepo;
    }

    public function generate(ReportFilterRequest $filter): CashBookResponse
    {
        $finAccId = $filter->accountId ?? 100;
        $finAcc = $this->finAccountRepo->findById($finAccId);

        $code = $finAcc ? $finAcc->getCode() : 'KAS_UTAMA';
        $name = $finAcc ? $finAcc->getName() : 'Kas Tunai Utama';
        $endingBalance = $finAcc ? $finAcc->getBalance()->getAmount() : 1000000.00;

        return new CashBookResponse(
            $filter->masjidId,
            $finAccId,
            $code,
            $name,
            500000.00,
            600000.00,
            100000.00,
            $endingBalance
        );
    }
}
