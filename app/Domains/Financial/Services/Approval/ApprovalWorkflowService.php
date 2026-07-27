<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Approval;

use App\Domains\Financial\Entities\ApprovalLog;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class ApprovalWorkflowService
{
    private ApprovalStateMachine $stateMachine;
    private ApprovalPolicy $policy;
    private FinancialTransactionRepositoryInterface $trxRepo;
    private FinancialUnitOfWork $uow;

    /** @var ApprovalLog[] */
    private array $history = [];

    public function __construct(
        ApprovalStateMachine $stateMachine,
        ApprovalPolicy $policy,
        FinancialTransactionRepositoryInterface $trxRepo,
        FinancialUnitOfWork $uow
    ) {
        $this->stateMachine = $stateMachine;
        $this->policy = $policy;
        $this->trxRepo = $trxRepo;
        $this->uow = $uow;
    }

    public function submit(FinancialTransaction $transaction, string $userId): void
    {
        $this->stateMachine->assertTransitionAllowed($transaction->getStatus(), 'PENDING_APPROVAL');
        
        $transaction->submitForApproval();
        $this->logApproval($transaction, 'SUBMIT', $userId, 'Submitted for DKM approval');

        $this->persist($transaction);
    }

    public function approve(FinancialTransaction $transaction, string $userId, string $role): void
    {
        $this->stateMachine->assertTransitionAllowed($transaction->getStatus(), 'APPROVED');
        $this->policy->assertCanApprove($userId, $role);

        $transaction->approve($userId);
        $this->logApproval($transaction, 'APPROVE', $userId, 'Approved by ' . $role);

        $this->persist($transaction);
    }

    public function reject(FinancialTransaction $transaction, string $userId, string $role, ?string $notes = null): void
    {
        $this->stateMachine->assertTransitionAllowed($transaction->getStatus(), 'REJECTED');
        $this->policy->assertCanReject($userId, $role);

        $transaction->reject($userId, $notes);
        $this->logApproval($transaction, 'REJECT', $userId, $notes ?? 'Rejected by ' . $role);

        $this->persist($transaction);
    }

    public function resubmit(FinancialTransaction $transaction, string $userId): void
    {
        $this->stateMachine->assertTransitionAllowed($transaction->getStatus(), 'PENDING_APPROVAL');

        $transaction->submitForApproval();
        $this->logApproval($transaction, 'RESUBMIT', $userId, 'Re-submitted for DKM approval');

        $this->persist($transaction);
    }

    public function cancel(FinancialTransaction $transaction, string $userId): void
    {
        $this->stateMachine->assertTransitionAllowed($transaction->getStatus(), 'CANCELLED');

        $transaction->cancel();
        $this->logApproval($transaction, 'CANCEL', $userId, 'Cancelled by operator');

        $this->persist($transaction);
    }

    /**
     * @return ApprovalLog[]
     */
    public function getHistoryForTransaction(int $transactionId): array
    {
        return array_filter($this->history, fn(ApprovalLog $log) => $log->getTransactionId() === $transactionId);
    }

    private function logApproval(FinancialTransaction $transaction, string $action, string $userId, ?string $notes = null): void
    {
        $log = new ApprovalLog(
            count($this->history) + 1,
            (int) $transaction->getId(),
            $userId,
            $action,
            $notes,
            date('Y-m-d H:i:s')
        );
        $this->history[] = $log;
    }

    private function persist(FinancialTransaction $transaction): void
    {
        $this->uow->begin();
        try {
            $savedTrx = $this->trxRepo->save($transaction);
            $this->uow->collectEvents($savedTrx);
            $this->uow->commit();
        } catch (\Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}
