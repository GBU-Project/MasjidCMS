<?php

namespace Tests\Integration;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Repositories\Contracts\FinancialAccountRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\FundRepositoryInterface;
use App\Domains\Financial\Repositories\Contracts\JournalEntryRepositoryInterface;
use App\Domains\Financial\Services\Posting\FinancialPostingEngine;
use App\Domains\Financial\Services\Posting\JournalBuilder;
use App\Domains\Financial\Services\Posting\LedgerPostingService;
use App\Domains\Financial\Services\Posting\PostingPolicy;
use App\Domains\Financial\Services\Posting\PostingValidator;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use PHPUnit\Framework\TestCase;

class FinancialPostingConcurrencyTest extends TestCase
{
    public function testSequentialConcurrentPostingsPreserveAccountBalance(): void
    {
        $fund = new Fund(1, 'f-1', 'm-1', new FundCode('GENERAL'), 'Kas Umum', 'UNRESTRICTED');
        $financialAccount = new FinancialAccount(100, 'fa-1', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', null, null, new Money(1000000.00));

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findByIdForUpdate')->willReturn($fund);
        $fundRepo->method('findById')->willReturn($fund);

        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findByIdForUpdate')->willReturn($financialAccount);
        $finAccRepo->method('findById')->willReturn($financialAccount);
        $finAccRepo->method('save')->willReturnArgument(0);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);
        $journalRepo->method('save')->willReturnArgument(0);

        $uow = new FinancialUnitOfWork();

        $engine = new FinancialPostingEngine(
            new PostingPolicy(),
            new PostingValidator(),
            new JournalBuilder(),
            new LedgerPostingService(),
            $fundRepo,
            $finAccRepo,
            $trxRepo,
            $journalRepo,
            $uow
        );

        // Simulation 1: First posting +250,000
        $trx1 = FinancialTransactionFactory::createDraft(
            'trx-uuid-conc-1', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00081'), 'INCOME', new Money(250000.00), '2026-07-27 10:00:00'
        );
        $engine->postTransaction($trx1, new JournalNumber('JRN-202607-00081'), 101);

        // Simulation 2: Second posting +350,000 on locked balance inside transaction boundary
        $trx2 = FinancialTransactionFactory::createDraft(
            'trx-uuid-conc-2', 'm-1', 1, 401, 100,
            new TransactionNumber('TRX-202607-00082'), 'INCOME', new Money(350000.00), '2026-07-27 10:00:00'
        );
        $engine->postTransaction($trx2, new JournalNumber('JRN-202607-00082'), 101);

        // Total expected balance: 1,000,000 + 250,000 + 350,000 = 1,600,000 (No Lost Update)
        $this->assertSame(1600000.00, $financialAccount->getBalance()->getAmount());
    }
}
