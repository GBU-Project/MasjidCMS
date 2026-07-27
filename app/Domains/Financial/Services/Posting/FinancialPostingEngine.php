<?php

declare(strict_types=1);

namespace App\Domains\Financial\Services\Posting;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;

use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;

class FinancialPostingEngine
{
    private PostingPolicy $policy;
    private PostingValidator $validator;
    private JournalBuilder $journalBuilder;
    private LedgerPostingService $ledgerPostingService;
    private FundRepositoryInterface $fundRepo;
    private FinancialAccountRepositoryInterface $finAccountRepo;
    private FinancialTransactionRepositoryInterface $trxRepo;
    private JournalEntryRepositoryInterface $journalRepo;
    private FinancialUnitOfWork $uow;

    public function __construct(
        PostingPolicy $policy,
        PostingValidator $validator,
        JournalBuilder $journalBuilder,
        LedgerPostingService $ledgerPostingService,
        FundRepositoryInterface $fundRepo,
        FinancialAccountRepositoryInterface $finAccountRepo,
        FinancialTransactionRepositoryInterface $trxRepo,
        JournalEntryRepositoryInterface $journalRepo,
        FinancialUnitOfWork $uow
    ) {
        $this->policy = $policy;
        $this->validator = $validator;
        $this->journalBuilder = $journalBuilder;
        $this->ledgerPostingService = $ledgerPostingService;
        $this->fundRepo = $fundRepo;
        $this->finAccountRepo = $finAccountRepo;
        $this->trxRepo = $trxRepo;
        $this->journalRepo = $journalRepo;
        $this->uow = $uow;
    }

    public function postTransaction(
        FinancialTransaction $transaction,
        JournalNumber $journalNo,
        int $cashAccountId
    ): JournalEntry {
        // 1. Policy Check
        $this->policy->assertCanPost($transaction);

        // 2. Fetch Fund & FinancialAccount
        $fund = $this->fundRepo->findById($transaction->getFundId());
        $finAccount = $this->finAccountRepo->findById($transaction->getFinancialAccountId());

        if ($fund && $finAccount) {
            // 3. Validation Check
            $this->validator->validate($transaction, $fund, $finAccount);

            // 4. Update Cached Balance
            $this->ledgerPostingService->updateCachedAccountBalance($finAccount, $transaction);
        }

        // 5. Update Transaction Status to POSTED
        $now = date('Y-m-d H:i:s');
        $transaction->post($now);

        // 6. Build Double Entry Journal
        $journal = $this->journalBuilder->buildFromTransaction($transaction, $journalNo, $cashAccountId);

        // 7. Atomic Persistence Boundary via UnitOfWork
        $this->uow->begin();
        try {
            if ($finAccount) {
                $this->finAccountRepo->save($finAccount);
            }
            $savedTrx = $this->trxRepo->save($transaction);
            $savedJournal = $this->journalRepo->save($journal);

            $this->uow->collectEvents($savedTrx);
            $this->uow->collectEvents($savedJournal);

            $this->uow->commit();
        } catch (\Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }

        return $savedJournal;
    }

    public function voidPosting(
        FinancialTransaction $transaction,
        JournalNumber $reversalJournalNo
    ): JournalEntry {
        // 1. Fetch Original Journal
        $originalJournal = $this->journalRepo->findByTransactionId((int) $transaction->getId());

        // 2. Fetch FinancialAccount
        $finAccount = $this->finAccountRepo->findById($transaction->getFinancialAccountId());
        if ($finAccount) {
            $this->ledgerPostingService->reverseAccountBalance($finAccount, $transaction);
        }

        // 3. Update Status to VOID
        $transaction->voidTransaction();

        // 4. Build Reversal Journal
        $reversalJournal = $this->journalBuilder->buildReversalJournal(
            $originalJournal,
            $reversalJournalNo,
            date('Y-m-d H:i:s')
        );

        // 5. Atomic Persistence Boundary via UnitOfWork
        $this->uow->begin();
        try {
            if ($finAccount) {
                $this->finAccountRepo->save($finAccount);
            }
            $savedTrx = $this->trxRepo->save($transaction);
            $savedReversalJournal = $this->journalRepo->save($reversalJournal);

            $this->uow->collectEvents($savedTrx);
            $this->uow->collectEvents($savedReversalJournal);

            $this->uow->commit();
        } catch (\Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }

        return $savedReversalJournal;
    }
}
