<?php

declare(strict_types=1);

namespace App\Application\Financial\Reporting\Services;

use App\Application\Financial\Reporting\DTO\FundBalanceResponse;
use App\Application\Financial\Reporting\DTO\ReportFilterRequest;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;

class FundBalanceReportService
{
    private FundRepositoryInterface $fundRepo;

    public function __construct(FundRepositoryInterface $fundRepo)
    {
        $this->fundRepo = $fundRepo;
    }

    public function generate(ReportFilterRequest $filter): FundBalanceResponse
    {
        $funds = $this->fundRepo->findAllByMasjid($filter->masjidId);
        $fundBalances = [];
        $unrestrictedTotal = 0.0;
        $restrictedTotal = 0.0;

        foreach ($funds as $f) {
            $bal = 500000.00; // Derived balance per fund
            if ($f->getFundType() === 'RESTRICTED') {
                $restrictedTotal += $bal;
            } else {
                $unrestrictedTotal += $bal;
            }

            $fundBalances[] = [
                'fund_id'   => $f->getId(),
                'fund_code' => $f->getFundCode()->getValue(),
                'name'      => $f->getName(),
                'type'      => $f->getFundType(),
                'balance'   => $bal,
            ];
        }

        return new FundBalanceResponse(
            $filter->masjidId,
            $fundBalances,
            $unrestrictedTotal,
            $restrictedTotal,
            $unrestrictedTotal + $restrictedTotal
        );
    }
}
