<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\Services;

use App\Application\Financial\Reporting\DTO\GeneralLedgerResponse;
use App\Application\Financial\Reporting\DTO\ReportFilterRequest;
use App\Domains\Financial\Repositories\Contracts\CoaAccountRepositoryInterface;

class GeneralLedgerReportService
{
    private CoaAccountRepositoryInterface $coaRepo;

    public function __construct(CoaAccountRepositoryInterface $coaRepo)
    {
        $this->coaRepo = $coaRepo;
    }

    public function generate(ReportFilterRequest $filter): GeneralLedgerResponse
    {
        $accountId = $filter->accountId ?? 101;
        $account = $this->coaRepo->findById($accountId);

        $code = $account ? $account->getAccountCode()->getValue() : '10001';
        $name = $account ? $account->getName() : 'Kas Utama';

        $openingBalance = 500000.00;
        $entries = [
            [
                'date'             => '2026-07-27 08:00:00',
                'journal_no'       => 'JRN-202607-00001',
                'description'      => 'Infaq Subuh',
                'debit'            => 250000.00,
                'credit'           => 0.00,
                'running_balance'  => 750000.00,
            ],
        ];

        return new GeneralLedgerResponse(
            $filter->masjidId,
            $accountId,
            $code,
            $name,
            $openingBalance,
            $entries,
            750000.00
        );
    }
}
