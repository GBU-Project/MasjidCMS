<?php

namespace Tests\Unit;

use App\Application\Financial\Reporting\DTO\ReportFilterRequest;
use App\Application\Financial\Reporting\Services\CashBookReportService;
use App\Application\Financial\Reporting\Services\FundBalanceReportService;
use App\Application\Financial\Reporting\Services\GeneralLedgerReportService;
use App\Application\Financial\Reporting\Services\IncomeExpenseReportService;
use App\Application\Financial\Reporting\Services\TransactionHistoryReportService;
use App\Application\Financial\Reporting\Services\TrialBalanceReportService;
use App\Domains\Financial\Entities\CoaAccount;
use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\AccountCode;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Repositories\Contracts\CoaAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FinancialReportingEngineRc1Test extends TestCase
{
    public function testTrialBalanceReportGenerationAndBalancing(): void
    {
        $coaAcc1 = new CoaAccount(1, 'coa-1', 'm-1', new AccountCode('10001'), 'Kas Utama', 'ASSET', true);
        $coaAcc2 = new CoaAccount(2, 'coa-2', 'm-1', new AccountCode('40001'), 'Infaq Jamaah', 'REVENUE', true);

        $coaRepo = $this->createMock(CoaAccountRepositoryInterface::class);
        $coaRepo->method('findAllByMasjid')->willReturn([$coaAcc1, $coaAcc2]);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);

        $service = new TrialBalanceReportService($coaRepo, $journalRepo);
        $filter = new ReportFilterRequest('m-1', '2026-07-01', '2026-07-31');

        $report = $service->generate($filter);

        $this->assertTrue($report->isBalanced);
        $this->assertSame($report->totalDebit, $report->totalCredit);
        $this->assertCount(2, $report->accountBalances);
    }

    public function testGeneralLedgerReportGeneration(): void
    {
        $coaAcc = new CoaAccount(1, 'coa-1', 'm-1', new AccountCode('10001'), 'Kas Utama', 'ASSET', true);
        $coaRepo = $this->createMock(CoaAccountRepositoryInterface::class);
        $coaRepo->method('findById')->willReturn($coaAcc);

        $service = new GeneralLedgerReportService($coaRepo);
        $filter = new ReportFilterRequest('m-1', '2026-07-01', '2026-07-31', null, 1);

        $report = $service->generate($filter);

        $this->assertSame('10001', $report->accountCode);
        $this->assertSame(500000.00, $report->openingBalance);
        $this->assertSame(750000.00, $report->closingBalance);
    }

    public function testCashBookReportGeneration(): void
    {
        $finAcc = new FinancialAccount(100, 'fa-1', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', null, null, new Money(1000000.00));
        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($finAcc);

        $service = new CashBookReportService($finAccRepo);
        $filter = new ReportFilterRequest('m-1', null, null, null, 100);

        $report = $service->generate($filter);

        $this->assertSame('KAS_UTAMA', $report->accountCode);
        $this->assertSame(1000000.00, $report->endingBalance);
    }

    public function testFundBalanceReportGeneration(): void
    {
        $fund1 = new Fund(1, 'f-1', 'm-1', new FundCode('GENERAL'), 'Kas Operasional', 'UNRESTRICTED');
        $fund2 = new Fund(2, 'f-2', 'm-1', new FundCode('ZAKAT'), 'Dana Zakat', 'RESTRICTED');

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findAllByMasjid')->willReturn([$fund1, $fund2]);

        $service = new FundBalanceReportService($fundRepo);
        $filter = new ReportFilterRequest('m-1');

        $report = $service->generate($filter);

        $this->assertSame(500000.00, $report->totalUnrestrictedBalance);
        $this->assertSame(500000.00, $report->totalRestrictedBalance);
        $this->assertSame(1000000.00, $report->grandTotalBalance);
    }

    public function testIncomeExpenseReportGeneration(): void
    {
        $service = new IncomeExpenseReportService();
        $filter = new ReportFilterRequest('m-1', '2026-07-01', '2026-07-31');

        $report = $service->generate($filter);

        $this->assertSame(1000000.00, $report->totalIncome);
        $this->assertSame(500000.00, $report->totalExpense);
        $this->assertSame(500000.00, $report->netSurplusDeficit);
    }

    public function testTransactionHistoryReportFiltering(): void
    {
        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $service = new TransactionHistoryReportService($trxRepo);

        $filter = new ReportFilterRequest('m-1', '2026-07-01', '2026-07-31', 1, null, null, 'POSTED');
        $report = $service->generate($filter);

        $this->assertSame('m-1', $report->masjidId);
        $this->assertIsArray($report->transactions);
    }
}
