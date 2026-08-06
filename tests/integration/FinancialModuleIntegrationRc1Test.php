<?php

namespace Tests\Integration;

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
use App\Controllers\Api\FinancialApiController;
use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\Fund;

use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;

use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Domains\Financial\Services\Approval\ApprovalPolicy;
use App\Domains\Financial\Services\Approval\ApprovalStateMachine;
use App\Domains\Financial\Services\Approval\ApprovalWorkflowService;
use App\Domains\Financial\Services\FinancialDomainService;
use App\Domains\Financial\Services\Posting\FinancialPostingEngine;
use App\Domains\Financial\Services\Posting\JournalBuilder;
use App\Domains\Financial\Services\Posting\LedgerPostingService;
use App\Domains\Financial\Services\Posting\PostingPolicy;
use App\Domains\Financial\Services\Posting\PostingValidator;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use PHPUnit\Framework\TestCase;

class FinancialModuleIntegrationRc1Test extends TestCase
{
    private Fund $fundGeneral;
    private Fund $fundZakat;
    private FinancialAccount $financialAccount;
    private FinancialUnitOfWork $uow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fundGeneral = new Fund(1, 'f-gen-1', 'm-1', new FundCode('GENERAL'), 'Kas Umum', 'UNRESTRICTED');
        $this->fundZakat = new Fund(2, 'f-zakat-2', 'm-1', new FundCode('ZAKAT'), 'Dana Zakat', 'RESTRICTED');
        $this->financialAccount = new FinancialAccount(100, 'fa-1', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', null, null, new Money(1000000.00));
        $this->uow = new FinancialUnitOfWork();
    }

    // Scenario 1: Create Transaction
    public function testScenario1CreateTransaction(): void
    {
        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $service = new CreateTransactionApplicationService($trxRepo, $this->uow);
        $req = new CreateTransactionRequest(
            'm-1', 1, 401, 100, 'TRX-202607-00001', 'INCOME', 250000.00, '2026-07-27 10:00:00',
            null, null, null, null, null, 'CASH', 'Infaq Subuh', 'op-1'
        );

        $res = $service->execute($req);
        $this->assertSame('DRAFT', $res->status);
        $this->assertSame(250000.00, $res->amount);
    }

    // Scenario 2 & 3: Submit & Approve Transaction
    public function testScenario2And3SubmitAndApproveTransaction(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-2', 'm-1', 1, 501, 100,
            new TransactionNumber('TRX-202607-00002'), 'EXPENSE', new Money(150000.00), '2026-07-27 10:00:00'
        );

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $workflowService = new ApprovalWorkflowService(new ApprovalStateMachine(), new ApprovalPolicy(), $trxRepo, $this->uow);

        // Submit
        $workflowService->submit($trx, 'user-op-1');
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());

        // Approve
        $workflowService->approve($trx, 'user-treasurer-1', 'Treasurer');
        $this->assertSame('APPROVED', $trx->getStatus());
        $this->assertSame('user-treasurer-1', $trx->getApprovedBy());
    }

    // Scenario 4 & 5: Reject & Re-Submit Transaction
    public function testScenario4And5RejectAndResubmitTransaction(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-4', 'm-1', 1, 501, 100,
            new TransactionNumber('TRX-202607-00004'), 'EXPENSE', new Money(200000.00), '2026-07-27 10:00:00'
        );

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $workflowService = new ApprovalWorkflowService(new ApprovalStateMachine(), new ApprovalPolicy(), $trxRepo, $this->uow);

        $workflowService->submit($trx, 'user-op-1');
        $workflowService->reject($trx, 'user-chair-1', 'Chairman', 'Nota fisik belum dilampirkan');
        $this->assertSame('REJECTED', $trx->getStatus());

        $workflowService->resubmit($trx, 'user-op-1');
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());
    }

    // Scenario 6: Post Transaction & Double Entry Validation
    public function testScenario6PostTransactionAndDoubleEntry(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-6', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00006'), 'INCOME', new Money(500000.00), '2026-07-27 10:00:00'
        );

        $trx->submitForApproval();
        $trx->approve('user-checker');

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findById')->willReturn($this->fundGeneral);

        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($this->financialAccount);
        $finAccRepo->method('save')->willReturnArgument(0);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('findByUuid')->willReturn($trx);
        $trxRepo->method('save')->willReturnArgument(0);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);
        $journalRepo->method('save')->willReturnArgument(0);

        $engine = new FinancialPostingEngine(
            new PostingPolicy(), new PostingValidator(), new JournalBuilder(),
            new LedgerPostingService(), $fundRepo, $finAccRepo, $trxRepo, $journalRepo, $this->uow
        );

        $postService = new PostTransactionApplicationService($trxRepo, $engine, $this->uow);
        $res = $postService->execute(new PostTransactionRequest('trx-int-uuid-6', 'JRN-202607-00006', 101));

        $this->assertSame('POSTED', $res->status);
        // Balance updated: 1,000,000 + 500,000 = 1,500,000
        $this->assertSame(1500000.00, $this->financialAccount->getBalance()->getAmount());
    }

    // Scenario 7: Void Transaction & Reversal
    public function testScenario7VoidTransactionAndReversal(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-7', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00007'), 'INCOME', new Money(300000.00), '2026-07-27 10:00:00'
        );
        $trx->submitForApproval();
        $trx->approve('user-checker');
        $trx->post('2026-07-27 10:00:00');

        $journalBuilder = new JournalBuilder();
        $originalJournal = $journalBuilder->buildFromTransaction($trx, new JournalNumber('JRN-202607-00007'), 101);

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($this->financialAccount);
        $finAccRepo->method('save')->willReturnArgument(0);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('findByUuid')->willReturn($trx);
        $trxRepo->method('save')->willReturnArgument(0);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);
        $journalRepo->method('findByTransactionId')->willReturn($originalJournal);
        $journalRepo->method('save')->willReturnArgument(0);

        $engine = new FinancialPostingEngine(
            new PostingPolicy(), new PostingValidator(), $journalBuilder,
            new LedgerPostingService(), $fundRepo, $finAccRepo, $trxRepo, $journalRepo, $this->uow
        );

        $voidService = new VoidTransactionApplicationService($trxRepo, $engine, $this->uow);
        $res = $voidService->execute(new VoidTransactionRequest('trx-int-uuid-7', 'JRN-202607-00077'));

        $this->assertSame('VOID', $res->status);
    }

    // Scenario 8: Transfer Fund
    public function testScenario8TransferFund(): void
    {
        $fundTarget = new Fund(2, 'f-target', 'm-1', new FundCode('BUILDING'), 'Dana Pembangunan', 'UNRESTRICTED');

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findById')->will($this->returnValueMap([
            [1, $this->fundGeneral],
            [2, $fundTarget],
        ]));

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $service = new TransferFundApplicationService($fundRepo, $trxRepo, new FinancialDomainService(), $this->uow);
        $req = new TransferFundRequest('m-1', 1, 2, 100, 101, 102, 'TRX-202607-00008', 400000.00, '2026-07-27 10:00:00');

        $res = $service->execute($req);
        $this->assertSame('TRANSFER', $res->transactionType);
        $this->assertSame(400000.00, $res->amount);
    }

    // Scenario 9: Invalid State Transition
    public function testScenario9InvalidStateTransition(): void
    {
        $stateMachine = new ApprovalStateMachine();
        $this->expectException(BusinessRuleException::class);
        $stateMachine->assertTransitionAllowed('VOID', 'APPROVED');
    }

    // Scenario 10: Unauthorized Approval
    public function testScenario10UnauthorizedApproval(): void
    {
        $policy = new ApprovalPolicy();
        $this->expectException(BusinessRuleException::class);
        $policy->assertCanApprove('user-jamaah-9', 'Jamaah');
    }

    // Scenario 11: Double Entry Validation
    public function testScenario11DoubleEntryValidation(): void
    {
        $builder = new JournalBuilder();
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-11', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00011'), 'INCOME', new Money(100000.00), '2026-07-27 10:00:00'
        );

        $journal = $builder->buildFromTransaction($trx, new JournalNumber('JRN-202607-00011'), 101);
        $this->assertTrue($journal->isBalanced());
        $this->assertSame($journal->getTotalDebit(), $journal->getTotalCredit());
    }

    // Scenario 12: Restricted Fund Validation
    public function testScenario12RestrictedFundValidation(): void
    {
        $domainService = new FinancialDomainService();
        $this->expectException(BusinessRuleException::class);
        $domainService->validateInterFundTransfer($this->fundZakat, $this->fundGeneral, new Money(50000.00));
    }

    // Scenario 13: Rollback on Failure
    public function testScenario13RollbackOnFailure(): void
    {
        $uow = new FinancialUnitOfWork();
        $uow->begin();
        $this->assertTrue($uow->isInTransaction());

        $uow->rollback();
        $this->assertFalse($uow->isInTransaction());
    }

    // Scenario 14: Domain Event Recording
    public function testScenario14DomainEventRecording(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-14', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00014'), 'INCOME', new Money(50000.00), '2026-07-27 10:00:00'
        );

        $trx->submitForApproval();
        $events = $trx->releaseEvents();

        $this->assertCount(1, $events);
        $this->assertSame('financial.transaction.submitted', $events[0]->eventName());
    }

    // Scenario 15: Approval History Recording
    public function testScenario15ApprovalHistoryRecording(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-int-uuid-15', 'm-1', 1, 501, 100,
            new TransactionNumber('TRX-202607-00015'), 'EXPENSE', new Money(75000.00), '2026-07-27 10:00:00'
        );
        $refProp = new \ReflectionProperty(FinancialTransaction::class, 'id');
        $refProp->setAccessible(true);
        $refProp->setValue($trx, 15);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $workflowService = new ApprovalWorkflowService(new ApprovalStateMachine(), new ApprovalPolicy(), $trxRepo, $this->uow);
        $workflowService->submit($trx, 'user-op-1');
        $workflowService->approve($trx, 'user-treasurer-1', 'Treasurer');

        $history = $workflowService->getHistoryForTransaction(15);
        $this->assertCount(2, $history);
    }
}
