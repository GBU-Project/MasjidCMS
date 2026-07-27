<?php

declare(strict_types=1);

namespace App\Application\Financial\Services;

use App\Application\Financial\DTO\ApproveTransactionRequest;
use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class ApproveTransactionApplicationService
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

    public function execute(ApproveTransactionRequest $request): FinancialTransactionResponse
    {
        $transaction = $this->trxRepo->findByUuid($request->transactionUuid);
        if (!$transaction) {
            throw new EntityNotFoundException("Transaksi dengan UUID [{$request->transactionUuid}] tidak ditemukan.");
        }

        $transaction->approve($request->approverUserId);

        $this->uow->begin();
        try {
            $savedTrx = $this->trxRepo->save($transaction);
            $this->uow->collectEvents($savedTrx);
            $this->uow->commit();
        } catch (\Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }

        $events = $this->uow->releaseEvents();

        return FinancialTransactionResponse::fromEntity($savedTrx);
    }
}
