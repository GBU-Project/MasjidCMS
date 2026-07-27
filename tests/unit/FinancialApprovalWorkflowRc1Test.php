<?php

namespace Tests\Unit;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Events\ApprovalGrantedEvent;
use App\Domains\Financial\Events\ApprovalRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionApprovedEvent;
use App\Domains\Financial\Events\FinancialTransactionRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionSubmittedEvent;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Services\Approval\ApprovalPolicy;
use App\Domains\Financial\Services\Approval\ApprovalStateMachine;
use App\Domains\Financial\Services\Approval\ApprovalWorkflowService;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use PHPUnit\Framework\TestCase;

class FinancialApprovalWorkflowRc1Test extends TestCase
{
    private ApprovalStateMachine $stateMachine;
    private ApprovalPolicy $policy;
    private FinancialUnitOfWork $uow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = new ApprovalStateMachine();
        $this->policy = new ApprovalPolicy();
        $this->uow = new FinancialUnitOfWork();
    }

    public function testStateMachineValidAndInvalidTransitions(): void
    {
        $this->assertTrue($this->stateMachine->canTransition('DRAFT', 'PENDING_APPROVAL'));
        $this->assertTrue($this->stateMachine->canTransition('PENDING_APPROVAL', 'APPROVED'));
        $this->assertTrue($this->stateMachine->canTransition('PENDING_APPROVAL', 'REJECTED'));
        $this->assertTrue($this->stateMachine->canTransition('APPROVED', 'POSTED'));
        $this->assertTrue($this->stateMachine->canTransition('POSTED', 'VOID'));

        $this->assertFalse($this->stateMachine->canTransition('DRAFT', 'VOID'));
        $this->assertFalse($this->stateMachine->canTransition('POSTED', 'APPROVED'));

        $this->expectException(BusinessRuleException::class);
        $this->stateMachine->assertTransitionAllowed('VOID', 'APPROVED');
    }

    public function testApprovalPolicyRolePermissions(): void
    {
        $this->assertTrue($this->policy->isAuthorizedToApprove('Treasurer'));
        $this->assertTrue($this->policy->isAuthorizedToApprove('Finance Manager'));
        $this->assertTrue($this->policy->isAuthorizedToApprove('Chairman'));
        $this->assertTrue($this->policy->isAuthorizedToApprove('Super Admin'));

        $this->assertFalse($this->policy->isAuthorizedToApprove('Jamaah'));
        $this->assertFalse($this->policy->isAuthorizedToApprove('Staff'));

        $this->expectException(BusinessRuleException::class);
        $this->policy->assertCanApprove('user-jamaah-1', 'Jamaah');
    }

    public function testApprovalSuccessAndAuditLogHistory(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-700',
            'm-1',
            1,
            501,
            100,
            new TransactionNumber('TRX-202607-00700'),
            'EXPENSE',
            new Money(450000.00),
            '2026-07-27 10:00:00'
        );
        // Assign ID 700 for history filtering
        $refProp = new \ReflectionProperty(FinancialTransaction::class, 'id');
        $refProp->setAccessible(true);
        $refProp->setValue($trx, 700);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $workflowService = new ApprovalWorkflowService($this->stateMachine, $this->policy, $trxRepo, $this->uow);

        // 1. Submit
        $workflowService->submit($trx, 'user-op-1');
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());

        // 2. Approve by Treasurer
        $workflowService->approve($trx, 'user-treasurer-1', 'Treasurer');
        $this->assertSame('APPROVED', $trx->getStatus());
        $this->assertSame('user-treasurer-1', $trx->getApprovedBy());

        // 3. Verify History
        $history = $workflowService->getHistoryForTransaction(700);
        $this->assertCount(2, $history);
        $this->assertSame('SUBMIT', $history[0]->getAction());
        $this->assertSame('APPROVE', $history[1]->getAction());

        // 4. Verify Domain Events Collected
        $events = $this->uow->releaseEvents();
        $this->assertGreaterThanOrEqual(3, count($events));
    }

    public function testRejectionAndResubmitFlow(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-701',
            'm-1',
            1,
            501,
            100,
            new TransactionNumber('TRX-202607-00701'),
            'EXPENSE',
            new Money(600000.00),
            '2026-07-27 10:00:00'
        );

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $workflowService = new ApprovalWorkflowService($this->stateMachine, $this->policy, $trxRepo, $this->uow);

        // Submit -> Reject -> Resubmit -> Approve
        $workflowService->submit($trx, 'user-op-1');
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());

        $workflowService->reject($trx, 'user-chairman-1', 'Chairman', 'Nota kurang lengkap');
        $this->assertSame('REJECTED', $trx->getStatus());

        $workflowService->resubmit($trx, 'user-op-1');
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());

        $workflowService->approve($trx, 'user-fin-mgr', 'Finance Manager');
        $this->assertSame('APPROVED', $trx->getStatus());
    }
}
