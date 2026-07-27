<?php

namespace Tests\Unit;

use App\Domains\Financial\Entities\ValueObjects\JournalNumber;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Events\ApprovalGrantedEvent;
use App\Domains\Financial\Events\ApprovalRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionApprovedEvent;
use App\Domains\Financial\Events\FinancialTransactionPostedEvent;
use App\Domains\Financial\Events\FinancialTransactionRejectedEvent;
use App\Domains\Financial\Events\FinancialTransactionSubmittedEvent;
use App\Domains\Financial\Events\FinancialTransactionVoidedEvent;
use App\Domains\Financial\Events\FundTransferredEvent;
use App\Domains\Financial\Events\JournalEntryCreatedEvent;
use App\Domains\Financial\Events\JournalPostedEvent;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use App\Domains\Financial\Factories\JournalEntryFactory;
use PHPUnit\Framework\TestCase;

class FinancialDomainEventsRc1Test extends TestCase
{
    public function testDomainEventsCreationAndGetters(): void
    {
        $submittedEvt = new FinancialTransactionSubmittedEvent('evt-1', 'trx-uuid-1', ['amount' => 100000.00]);
        $this->assertSame('evt-1', $submittedEvt->eventId());
        $this->assertSame('trx-uuid-1', $submittedEvt->aggregateId());
        $this->assertSame('FinancialTransaction', $submittedEvt->aggregateType());
        $this->assertSame('financial.transaction.submitted', $submittedEvt->eventName());
        $this->assertSame(1, $submittedEvt->eventVersion());
        $this->assertSame(['amount' => 100000.00], $submittedEvt->payload());
        $this->assertNotEmpty($submittedEvt->occurredAt());

        $approvedEvt = new FinancialTransactionApprovedEvent('evt-2', 'trx-uuid-1', ['approver' => 'user-1']);
        $this->assertSame('financial.transaction.approved', $approvedEvt->eventName());

        $rejectedEvt = new FinancialTransactionRejectedEvent('evt-3', 'trx-uuid-1', ['reason' => 'insufficient docs']);
        $this->assertSame('financial.transaction.rejected', $rejectedEvt->eventName());

        $postedEvt = new FinancialTransactionPostedEvent('evt-4', 'trx-uuid-1', ['posted_at' => '2026-07-27 10:00:00']);
        $this->assertSame('financial.transaction.posted', $postedEvt->eventName());

        $voidedEvt = new FinancialTransactionVoidedEvent('evt-5', 'trx-uuid-1', ['reason' => 'reversal']);
        $this->assertSame('financial.transaction.voided', $voidedEvt->eventName());

        $jrnCreatedEvt = new JournalEntryCreatedEvent('evt-6', 'jrn-uuid-1', ['journal_no' => 'JRN-202607-00001']);
        $this->assertSame('financial.journal.created', $jrnCreatedEvt->eventName());

        $jrnPostedEvt = new JournalPostedEvent('evt-7', 'jrn-uuid-1', []);
        $this->assertSame('financial.journal.posted', $jrnPostedEvt->eventName());

        $fundTransferredEvt = new FundTransferredEvent('evt-8', 'fund-uuid-1', ['target_fund' => 'fund-uuid-2']);
        $this->assertSame('financial.fund.transferred', $fundTransferredEvt->eventName());

        $grantEvt = new ApprovalGrantedEvent('evt-9', 'trx-uuid-1', []);
        $this->assertSame('financial.approval.granted', $grantEvt->eventName());

        $rejectGrantEvt = new ApprovalRejectedEvent('evt-10', 'trx-uuid-1', []);
        $this->assertSame('financial.approval.rejected', $rejectGrantEvt->eventName());
    }

    public function testAggregateEventRecordingAndRelease(): void
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-100',
            'm-1',
            1,
            10,
            100,
            new TransactionNumber('TRX-202607-00001'),
            'INCOME',
            new Money(250000.00),
            '2026-07-27 10:00:00'
        );

        $this->assertCount(0, $trx->getRecordedEvents());

        $trx->submitForApproval();
        $this->assertCount(1, $trx->getRecordedEvents());
        $this->assertInstanceOf(FinancialTransactionSubmittedEvent::class, $trx->getRecordedEvents()[0]);

        $trx->approve('user-admin-1');
        // Submit (1) + Approve (1) + Granted (1) = 3 events
        $this->assertCount(3, $trx->getRecordedEvents());

        $releasedEvents = $trx->releaseEvents();
        $this->assertCount(3, $releasedEvents);
        // After release, aggregate queue should be empty
        $this->assertCount(0, $trx->getRecordedEvents());
    }

    public function testJournalEntryEventRecording(): void
    {
        $journal = JournalEntryFactory::createSimpleJournal(
            'jrn-uuid-100',
            1,
            new JournalNumber('JRN-202607-00001'),
            '2026-07-27 10:00:00',
            101,
            401,
            new Money(500000.00),
            'Infaq Kotak Jumat'
        );

        $events = $journal->releaseEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(JournalEntryCreatedEvent::class, $events[0]);
        $this->assertSame('JRN-202607-00001', $events[0]->payload()['journal_no']);
    }
}
