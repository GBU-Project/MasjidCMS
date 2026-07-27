<?php

declare(strict_types=1);

namespace App\Application\Financial\Services;

use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Application\Financial\DTO\TransferFundRequest;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Services\FinancialDomainService;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class TransferFundApplicationService
{
    private FundRepositoryInterface $fundRepo;
    private FinancialTransactionRepositoryInterface $trxRepo;
    private FinancialDomainService $domainService;
    private FinancialUnitOfWork $uow;

    public function __construct(
        FundRepositoryInterface $fundRepo,
        FinancialTransactionRepositoryInterface $trxRepo,
        FinancialDomainService $domainService,
        FinancialUnitOfWork $uow
    ) {
        $this->fundRepo = $fundRepo;
        $this->trxRepo = $trxRepo;
        $this->domainService = $domainService;
        $this->uow = $uow;
    }

    public function execute(TransferFundRequest $request): FinancialTransactionResponse
    {
        $sourceFund = $this->fundRepo->findById($request->sourceFundId);
        $targetFund = $this->fundRepo->findById($request->targetFundId);

        if (!$sourceFund || !$targetFund) {
            throw new EntityNotFoundException("Kantong dana asal/tujuan tidak ditemukan.");
        }

        // Domain Service Business Rule Check (BR-FIN-01 / BR-FIN-03)
        $this->domainService->validateInterFundTransfer($sourceFund, $targetFund, new Money($request->amount));

        $uuid = 'trx-' . bin2hex(random_bytes(8));
        $transaction = FinancialTransactionFactory::createDraft(
            $uuid,
            $request->masjidId,
            $request->sourceFundId,
            $request->transferAccountId,
            $request->sourceFinancialAccountId,
            new TransactionNumber($request->transactionNo),
            'TRANSFER',
            new Money($request->amount),
            $request->transactionDate,
            null,
            null,
            $request->description ?? "Transfer Fund {$sourceFund->getName()} -> {$targetFund->getName()}",
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

        return FinancialTransactionResponse::fromEntity($savedTrx);
    }
}
