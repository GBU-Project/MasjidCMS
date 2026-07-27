<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\Services;

use App\Application\Financial\Reporting\DTO\ReportFilterRequest;
use App\Application\Financial\Reporting\DTO\TransactionHistoryResponse;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;

class TransactionHistoryReportService
{
    private FinancialTransactionRepositoryInterface $trxRepo;

    public function __construct(FinancialTransactionRepositoryInterface $trxRepo)
    {
        $this->trxRepo = $trxRepo;
    }

    public function generate(ReportFilterRequest $filter): TransactionHistoryResponse
    {
        // Read model history listing
        $transactions = [];
        return new TransactionHistoryResponse($filter->masjidId, $transactions);
    }
}
