<?php

namespace App\Core\Audit\Listeners;

use App\Core\Audit\Services\AuditService;
use App\Core\Contracts\Events\DomainEventInterface;
use App\Core\Contracts\Events\EventListenerInterface;

/**
 * Class AuditEventListener
 *
 * Listener yang mendengarkan seluruh Domain Event dan mencatatnya ke Audit Engine.
 */
class AuditEventListener implements EventListenerInterface
{
    protected AuditService $auditService;

    public function __construct(?AuditService $auditService = null)
    {
        $this->auditService = $auditService ?? new AuditService();
    }

    /**
     * Memproses penerimaan Domain Event dan merekamnya sebagai AuditEntry.
     */
    public function handle(DomainEventInterface $event): void
    {
        $this->auditService->recordEvent($event);
    }
}
