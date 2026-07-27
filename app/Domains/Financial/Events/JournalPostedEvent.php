<?php

declare(strict_types=1);

namespace App\Domains\Financial\Events;

final class JournalPostedEvent extends AbstractFinancialDomainEvent
{
    public function __construct(string $eventId, string $journalUuid, array $payload = [])
    {
        parent::__construct(
            $eventId,
            $journalUuid,
            'JournalEntry',
            'financial.journal.posted',
            $payload
        );
    }
}
