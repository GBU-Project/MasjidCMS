<?php

namespace Tests\Unit;

use App\Application\Financial\DTO\ApproveTransactionRequest;
use App\Application\Financial\DTO\CreateTransactionRequest;
use App\Application\Financial\DTO\PostTransactionRequest;
use App\Application\Financial\DTO\RejectTransactionRequest;
use App\Application\Financial\DTO\TransferFundRequest;
use App\Application\Financial\DTO\VoidTransactionRequest;
use App\Application\Financial\Services\ApproveTransactionApplicationService;
use App\Application\Financial\Services\CreateTransactionApplicationService;
use App\Application\Financial\Services\PostTransactionApplicationService;
use App\Application\Financial\Services\RejectTransactionApplicationService;
use App\Application\Financial\Services\TransferFundApplicationService;
use App\Application\Financial\Services\VoidTransactionApplicationService;
use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Domains\Financial\Services\FinancialDomainService;
use App\Domains\Financial\Services\Posting\FinancialPostingEngine;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use PHPUnit\Framework\TestCase;

class FinancialApplicationServicesRc1Test extends TestCase
{
    private FinancialUnitOfWork $uow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uow = new FinancialUnitOfWork();
    }

    public function testCreateTransactionApplicationService(): void
    {
        $req = new CreateTransactionRequest(
            'm-1',
            1,
            401,
            100,
            'TRX-202607-00100',
            'INCOME',
            350000.00,
            '2026-07-27 10:00:00',
            null,
            null,
            null,
            null,
            null,
            'CASH',
            'Infaq Kotak Jumat',
            'user-op-1'
        );

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $service = new CreateTransactionApplicationService($trxRepo, $this->uow);
        $response = $service->execute($req);

        $this->assertSame('TRX-202607-00100', $response->transactionNo);
        $this->assertSame(350000.00, $response->amount);
        $this->assertSame('DRAFT', $response->status);
    }

    public function testApproveAndRejectTransactionApplicationService(): void
    {
        $draftTrx = FinancialTransactionFactory::createDraft(
            'trx-uuid-800',
            'm-1',
            1,
            501,
            100,
            new TransactionNumber('TRX-202607-00101'),
            'EXPENSE',
            new Money(100000.00),
            '2026-07-27 10:00:00'
        );
        $draftTrx->submitForApproval();

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('findByUuid')->willReturn($draftTrx);
        $trxRepo->method('save')->willReturnArgument(0);

        $approveService = new ApproveTransactionApplicationService($trxRepo, $this->uow);
        $approveReq = new ApproveTransactionRequest('trx-uuid-800', 'user-dkm-1');
        $approvedResponse = $approveService->execute($approveReq);

        $this->assertSame('APPROVED', $approvedResponse->status);
        $this->assertSame('user-dkm-1', $approvedResponse->approvedBy);
    }

    public function testTransferFundApplicationServiceZakatRestrictionFailure(): void
    {
        $zakatFund = new Fund(1, 'f-zakat', 'm-1', new FundCode('ZAKAT'), 'Dana Zakat', 'RESTRICTED');
        $generalFund = new Fund(2, 'f-gen', 'm-1', new FundCode('GENERAL'), 'Kas Umum', 'UNRESTRICTED');

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findById')->will($this->returnValueMap([
            [1, $zakatFund],
            [2, $generalFund],
        ]));

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $domainService = new FinancialDomainService();

        $transferService = new TransferFundApplicationService($fundRepo, $trxRepo, $domainService, $this->uow);

        $req = new TransferFundRequest(
            'm-1',
            1, // Zakat
            2, // General
            100,
            101,
            102,
            'TRX-202607-00105',
            50000.00,
            '2026-07-27 10:00:00'
        );

        $this->expectException(BusinessRuleException::class);
        $transferService->execute($req);
    }
}
