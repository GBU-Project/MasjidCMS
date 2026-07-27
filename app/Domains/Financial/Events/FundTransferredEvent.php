<?php

declare(strict_types=1);

namespace App\Domains\Financial\Events;

final class FundTransferredEvent extends AbstractFinancialDomainEvent
{
    public function __construct(string $eventId, string $sourceFundUuid, array $payload = [])
    {
        parent::__construct(
            $eventId,
            $sourceFundUuid,
            'Fund',
            'financial.fund.transferred',
            $payload
        );
    }
}
