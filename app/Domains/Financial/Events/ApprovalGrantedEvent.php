<?php

declare(strict_types=1);

namespace App\Domains\Financial\Events;

final class ApprovalGrantedEvent extends AbstractFinancialDomainEvent
{
    public function __construct(string $eventId, string $transactionUuid, array $payload = [])
    {
        parent::__construct(
            $eventId,
            $transactionUuid,
            'FinancialTransaction',
            'financial.approval.granted',
            $payload
        );
    }
}
