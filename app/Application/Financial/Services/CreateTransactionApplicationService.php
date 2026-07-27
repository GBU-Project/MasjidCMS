<?php

declare(strict_types=1);

namespace App\Application\Financial\Services;

use App\Application\Financial\DTO\CreateTransactionRequest;
use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class CreateTransactionApplicationService
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

    public function execute(CreateTransactionRequest $request): FinancialTransactionResponse
    {
        $uuid = 'trx-' . bin2hex(random_bytes(8));
        $transaction = FinancialTransactionFactory::createDraft(
            $uuid,
            $request->masjidId,
            $request->fundId,
            $request->accountId,
            $request->financialAccountId,
            new TransactionNumber($request->transactionNo),
            $request->transactionType,
            new Money($request->amount),
            $request->transactionDate,
            $request->programId,
            $request->jamaahId,
            $request->description,
            $request->createdBy
        );

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
        // Domain events released ready for event dispatcher

        return FinancialTransactionResponse::fromEntity($savedTrx);
    }
}
