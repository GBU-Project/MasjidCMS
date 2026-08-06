<?php

namespace Tests\Unit;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
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

class FinancialPostingEngineRc1Test extends TestCase
{
    private Fund $fund;
    private FinancialAccount $financialAccount;
    private PostingPolicy $policy;
    private PostingValidator $validator;
    private JournalBuilder $journalBuilder;
    private LedgerPostingService $ledgerService;
    private FinancialUnitOfWork $uow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fund = new Fund(1, 'f-1', 'm-1', new FundCode('GENERAL'), 'Kas Umum');
        $this->financialAccount = new FinancialAccount(100, 'fa-1', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', null, null, new Money(500000.00));

        $this->policy = new PostingPolicy();
        $this->validator = new PostingValidator();
        $this->journalBuilder = new JournalBuilder();
        $this->ledgerService = new LedgerPostingService();
        $this->uow = new FinancialUnitOfWork();
    }

    public function testPostingSuccessForIncomeTransaction(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-500',
            'm-1',
            1,
            401, // Income COA Account
            100, // Financial Account
            new TransactionNumber('TRX-202607-00050'),
            'INCOME',
            new Money(200000.00),
            '2026-07-27 10:00:00'
        );
        $trx->submitForApproval();
        $trx->approve('user-checker');

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findById')->willReturn($this->fund);

        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($this->financialAccount);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);
        $journalRepo->method('save')->willReturnArgument(0);

        $engine = new FinancialPostingEngine(
            $this->policy,
            $this->validator,
            $this->journalBuilder,
            $this->ledgerService,
            $fundRepo,
            $finAccRepo,
            $trxRepo,
            $journalRepo,
            $this->uow
        );

        $journal = $engine->postTransaction(
            $trx,
            new JournalNumber('JRN-202607-00050'),
            101 // Cash COA Account
        );

        $this->assertSame('POSTED', $trx->getStatus());
        $this->assertTrue($journal->isBalanced());
        $this->assertSame(200000.00, $journal->getTotalDebit());
        $this->assertSame(200000.00, $journal->getTotalCredit());
        // Cached balance updated: 500,000 + 200,000 = 700,000
        $this->assertSame(700000.00, $this->financialAccount->getBalance()->getAmount());
    }

    public function testPostingFailureForDeficitExpense(): void
    {
        $expenseTrx = FinancialTransactionFactory::createDraft(
            'trx-uuid-501',
            'm-1',
            1,
            501, // Expense COA Account
            100, // Financial Account (Balance = 500,000)
            new TransactionNumber('TRX-202607-00051'),
            'EXPENSE',
            new Money(900000.00), // Exceeds balance!
            '2026-07-27 10:00:00'
        );

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $fundRepo->method('findById')->willReturn($this->fund);

        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($this->financialAccount);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);

        $engine = new FinancialPostingEngine(
            $this->policy,
            $this->validator,
            $this->journalBuilder,
            $this->ledgerService,
            $fundRepo,
            $finAccRepo,
            $trxRepo,
            $journalRepo,
            $this->uow
        );

        $this->expectException(BusinessRuleException::class);
        $engine->postTransaction(
            $expenseTrx,
            new JournalNumber('JRN-202607-00051'),
            101
        );
    }

    public function testVoidPostingAndReversalJournal(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-502',
            'm-1',
            1,
            401,
            100,
            new TransactionNumber('TRX-202607-00052'),
            'INCOME',
            new Money(100000.00),
            '2026-07-27 10:00:00'
        );

        $originalJournal = $this->journalBuilder->buildFromTransaction(
            $trx,
            new JournalNumber('JRN-202607-00052'),
            101
        );

        $fundRepo = $this->createMock(FundRepositoryInterface::class);
        $finAccRepo = $this->createMock(FinancialAccountRepositoryInterface::class);
        $finAccRepo->method('findById')->willReturn($this->financialAccount);

        $trxRepo = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $trxRepo->method('save')->willReturnArgument(0);

        $journalRepo = $this->createMock(JournalEntryRepositoryInterface::class);
        $journalRepo->method('findByTransactionId')->willReturn($originalJournal);
        $journalRepo->method('save')->willReturnArgument(0);

        $engine = new FinancialPostingEngine(
            $this->policy,
            $this->validator,
            $this->journalBuilder,
            $this->ledgerService,
            $fundRepo,
            $finAccRepo,
            $trxRepo,
            $journalRepo,
            $this->uow
        );

        // Pre-post transaction
        $trx->submitForApproval();
        $trx->approve('user-checker');
        $trx->post('2026-07-27 10:00:00');
        $this->financialAccount->credit(new Money(100000.00)); // Balance = 600,000

        $reversalJournal = $engine->voidPosting($trx, new JournalNumber('JRN-202607-00053'));

        $this->assertSame('VOID', $trx->getStatus());
        $this->assertTrue($reversalJournal->isBalanced());
        // Reversal debit/credit lines swapped
        $this->assertSame(100000.00, $reversalJournal->getTotalDebit());
        // Balance reversed back: 600,000 - 100,000 = 500,000
        $this->assertSame(500000.00, $this->financialAccount->getBalance()->getAmount());
    }
}
