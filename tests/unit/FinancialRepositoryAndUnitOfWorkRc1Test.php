<?php

namespace Tests\Unit;

use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;
use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Events\FinancialTransactionSubmittedEvent;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Factories\JournalEntryFactory;
use App\Infrastructure\Persistence\Financial\Mappers\FundDataMapper;
use App\Infrastructure\Persistence\Financial\Repositories\FundRepository;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use PHPUnit\Framework\TestCase;

class FinancialRepositoryAndUnitOfWorkRc1Test extends TestCase
{
    public function testFundDataMapperBidirectionalConversion(): void
    {
        $dbRow = [
            'id'         => 10,
            'uuid'       => 'f-uuid-10',
            'masjid_id'  => 'm-1',
            'fund_code'  => 'ZAKAT',
            'name'       => 'Dana Zakat',
            'fund_type'  => 'RESTRICTED',
            'status'     => 'ACTIVE',
            'created_at' => '2026-07-27 10:00:00',
        ];

        $domain = FundDataMapper::toDomain($dbRow);
        $this->assertSame(10, $domain->getId());
        $this->assertSame('f-uuid-10', $domain->getUuid());
        $this->assertSame('ZAKAT', $domain->getFundCode()->getValue());
        $this->assertTrue($domain->isRestricted());

        $exportedRow = FundDataMapper::toDatabaseRow($domain);
        $this->assertSame('ZAKAT', $exportedRow['fund_code']);
        $this->assertSame('RESTRICTED', $exportedRow['fund_type']);
    }

    public function testUnitOfWorkEventCollectionAndRelease(): void
    {
        $uow = new FinancialUnitOfWork();

        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-200',
            'm-1',
            1,
            10,
            100,
            new TransactionNumber('TRX-202607-00005'),
            'INCOME',
            new Money(150000.00),
            '2026-07-27 10:00:00'
        );

        $trx->submitForApproval();
        $this->assertCount(1, $trx->getRecordedEvents());

        $uow->collectEvents($trx);
        // After UOW collects events, aggregate events are released into UOW
        $this->assertCount(0, $trx->getRecordedEvents());
        $this->assertCount(1, $uow->getUncommittedEvents());
        $this->assertInstanceOf(FinancialTransactionSubmittedEvent::class, $uow->getUncommittedEvents()[0]);

        $releasedEvents = $uow->releaseEvents();
        $this->assertCount(1, $releasedEvents);
        $this->assertCount(0, $uow->getUncommittedEvents());
    }

    public function testJournalEntryFactoryAndMapping(): void
    {
        $journal = JournalEntryFactory::createSimpleJournal(
            'jrn-uuid-300',
            5,
            new JournalNumber('JRN-202607-00005'),
            '2026-07-27 10:00:00',
            101,
            401,
            new Money(300000.00),
            'Infaq Jumat'
        );

        $this->assertTrue($journal->isBalanced());
        $this->assertSame(300000.00, $journal->getTotalDebit());
        $this->assertSame(300000.00, $journal->getTotalCredit());
        $this->assertCount(2, $journal->getDetails());
    }
}
