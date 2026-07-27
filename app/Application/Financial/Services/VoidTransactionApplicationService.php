<?php

declare(strict_types=1);

namespace App\Application\Financial\Services;

use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Application\Financial\DTO\VoidTransactionRequest;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Services\Posting\FinancialPostingEngine;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class VoidTransactionApplicationService
{
    private FinancialTransactionRepositoryInterface $trxRepo;
    private FinancialPostingEngine $postingEngine;
    private FinancialUnitOfWork $uow;

    public function __construct(
        FinancialTransactionRepositoryInterface $trxRepo,
        FinancialPostingEngine $postingEngine,
        FinancialUnitOfWork $uow
    ) {
        $this->trxRepo = $trxRepo;
        $this->postingEngine = $postingEngine;
        $this->uow = $uow;
    }

    public function execute(VoidTransactionRequest $request): FinancialTransactionResponse
    {
        $transaction = $this->trxRepo->findByUuid($request->transactionUuid);
        if (!$transaction) {
            throw new EntityNotFoundException("Transaksi dengan UUID [{$request->transactionUuid}] tidak ditemukan.");
        }

        $reversalJournalNo = new JournalNumber($request->reversalJournalNo);
        $reversalJournal = $this->postingEngine->voidPosting($transaction, $reversalJournalNo);

        $events = $this->uow->releaseEvents();

        return FinancialTransactionResponse::fromEntity($transaction);
    }
}
