<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\Services;

use App\Application\Financial\Reporting\DTO\IncomeExpenseResponse;
use App\Application\Financial\Reporting\DTO\ReportFilterRequest;

class IncomeExpenseReportService
{
    public function generate(ReportFilterRequest $filter): IncomeExpenseResponse
    {
        $period = ($filter->startDate && $filter->endDate)
            ? "{$filter->startDate} s/d {$filter->endDate}"
            : "All Periods";

        $incomeBreakdown = [
            ['code' => '40001', 'name' => 'Infaq Kotak Jumat', 'amount' => 750000.00],
            ['code' => '40002', 'name' => 'Donasi Jamaah', 'amount' => 250000.00],
        ];

        $expenseBreakdown = [
            ['code' => '50001', 'name' => 'Beban Kebersihan', 'amount' => 200000.00],
            ['code' => '50002', 'name' => 'Beban Listrik & Air', 'amount' => 300000.00],
        ];

        return new IncomeExpenseResponse(
            $filter->masjidId,
            $period,
            1000000.00,
            500000.00,
            $incomeBreakdown,
            $expenseBreakdown
        );
    }
}
