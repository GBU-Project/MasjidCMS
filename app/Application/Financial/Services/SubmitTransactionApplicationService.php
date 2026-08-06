<?php

declare(strict_types=1);

namespace App\Application\Financial\Services;

use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

/**
 * Moves a transaction from DRAFT to PENDING_APPROVAL.
 *
 * Added as part of the RC Blocker fix (see
 * docs/Audit/RC_BLOCKER_RESOLUTION_REPORT.md): the entity method
 * FinancialTransaction::submitForApproval() already existed, but no
 * Application Service exposed it, so no caller (Admin UI or API) could
 * legally move a transaction out of DRAFT. This follows the exact same
 * shape as ApproveTransactionApplicationService/RejectTransactionApplicationService
 * — it does not introduce a new workflow, it completes wiring for a
 * transition the domain layer already defined.
 */
class SubmitTransactionApplicationService
{
    private FinancialTransactionRepositoryInterface $trxRepo;
    private FinancialUnitOfWork $uow;

    public function __construct(
        FinancialTransactionRepositoryInterface $trxRepo,
        FinancialUnitOfWork $uow
    ) {
        $this->trxRepo = $trxRepo;
        $this->uow = $uow;
    }

    public function execute(string $transactionUuid): FinancialTransactionResponse
    {
        $transaction = $this->trxRepo->findByUuid($transactionUuid);
        if (!$transaction) {
            throw new EntityNotFoundException("Transaksi dengan UUID [{$transactionUuid}] tidak ditemukan.");
        }

        $transaction->submitForApproval();

        $this->uow->begin();
        try {
            $savedTrx = $this->trxRepo->save($transaction);
            $this->uow->collectEvents($savedTrx);
            $this->uow->commit();
        } catch (\Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }

        $this->uow->releaseEvents();

        return FinancialTransactionResponse::fromEntity($savedTrx);
    }
}
