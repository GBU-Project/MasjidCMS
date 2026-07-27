<?php

namespace Tests\Unit;

use App\Domains\Financial\Entities\FinancialAccount;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\JournalDetail;
use App\Domains\Financial\Entities\JournalEntry;
use App\Domains\Financial\Entities\ValueObjects\AccountCode;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\ProgramCode;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Exceptions\InvalidValueObjectException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Factories\JournalEntryFactory;
use App\Domains\Financial\Services\FinancialDomainService;
use App\Domains\Financial\Specifications\IsBalancedJournalSpecification;
use App\Domains\Financial\Specifications\IsRestrictedFundSpecification;
use PHPUnit\Framework\TestCase;

class FinancialDomainModelRc1Test extends TestCase
{
    public function testMoneyValueObjectValidations(): void
    {
        $money = new Money(150000.00);
        $this->assertSame(150000.00, $money->getAmount());
        $this->assertSame('IDR', $money->getCurrency());

        $this->expectException(InvalidValueObjectException::class);
        new Money(-500.00);
    }

    public function testTransactionNumberValidation(): void
    {
        $trxNo = new TransactionNumber('TRX-202607-00001');
        $this->assertSame('TRX-202607-00001', $trxNo->getValue());

        $this->expectException(InvalidValueObjectException::class);
        new TransactionNumber('INVALID-NO');
    }

    public function testFundEntityAndDeactivation(): void
    {
        $fund = new Fund(1, 'f-uuid-1', 'm-1', new FundCode('ZAKAT'), 'Dana Zakat', 'RESTRICTED', 'ACTIVE');
        $this->assertTrue($fund->isRestricted());
        $this->assertSame('ACTIVE', $fund->getStatus());

        $fund->deactivate();
        $this->assertSame('INACTIVE', $fund->getStatus());

        $this->expectException(BusinessRuleException::class);
        $fund->deactivate(); // Already inactive
    }

    public function testFinancialAccountDebitAndCredit(): void
    {
        $acc = new FinancialAccount(1, 'fa-1', 'm-1', 'KAS_UTAMA', 'Kas Tunai Utama', null, null, new Money(100000.00));
        $acc->credit(new Money(50000.00));
        $this->assertSame(150000.00, $acc->getBalance()->getAmount());

        $acc->debit(new Money(100000.00));
        $this->assertSame(50000.00, $acc->getBalance()->getAmount());

        $this->expectException(BusinessRuleException::class);
        $acc->debit(new Money(900000.00)); // Exceeds balance
    }

    public function testFinancialTransactionLifecycleAndImmutability(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-1',
            'm-1',
            1,
            10,
            100,
            new TransactionNumber('TRX-202607-00001'),
            'INCOME',
            new Money(500000.00),
            '2026-07-27 10:00:00'
        );

        $this->assertSame('DRAFT', $trx->getStatus());
        $this->assertFalse($trx->isImmutable());

        $trx->submitForApproval();
        $this->assertSame('PENDING_APPROVAL', $trx->getStatus());

        $trx->approve('user-admin-1');
        $this->assertSame('APPROVED', $trx->getStatus());
        $this->assertSame('user-admin-1', $trx->getApprovedBy());

        $trx->post('2026-07-27 10:05:00');
        $this->assertSame('POSTED', $trx->getStatus());
        $this->assertTrue($trx->isImmutable());

        $this->expectException(BusinessRuleException::class);
        $trx->cancel(); // Cannot cancel posted transaction
    }

    public function testJournalEntryDoubleEntryBalancing(): void
    {
        $journal = JournalEntryFactory::createSimpleJournal(
            'jrn-uuid-1',
            1,
            new JournalNumber('JRN-202607-00001'),
            '2026-07-27 10:00:00',
            101, // Debit Kas
            401, // Credit Infaq
            new Money(250000.00),
            'Penerimaan Infaq Kotak Jumat'
        );

        $this->assertTrue($journal->isBalanced());
        $this->assertSame(250000.00, $journal->getTotalDebit());
        $this->assertSame(250000.00, $journal->getTotalCredit());

        $spec = new IsBalancedJournalSpecification();
        $this->assertTrue($spec->isSatisfiedBy($journal));
    }

    public function testUnbalancedJournalThrowsBusinessRuleException(): void
    {
        $unbalancedJournal = new JournalEntry(
            1,
            'jrn-uuid-2',
            1,
            new JournalNumber('JRN-202607-00002'),
            '2026-07-27 10:00:00',
            'Unbalanced Test',
            [
                new JournalDetail(1, 1, 101, new Money(500000.00), new Money(0.00)),
                new JournalDetail(2, 1, 401, new Money(0.00), new Money(200000.00)),
            ]
        );

        $this->assertFalse($unbalancedJournal->isBalanced());

        $this->expectException(BusinessRuleException::class);
        $unbalancedJournal->assertBalanced();
    }

    public function testFinancialDomainServiceInterFundRestrictions(): void
    {
        $service = new FinancialDomainService();
        $zakatFund = new Fund(1, 'f-1', 'm-1', new FundCode('ZAKAT'), 'Dana Zakat', 'RESTRICTED');
        $generalFund = new Fund(2, 'f-2', 'm-1', new FundCode('GENERAL'), 'Kas Umum', 'UNRESTRICTED');

        $this->expectException(BusinessRuleException::class);
        $service->validateInterFundTransfer($zakatFund, $generalFund, new Money(100000.00));
    }
}
