<?php

namespace App\Core\Audit\Services;

use App\Core\Audit\AuditEntry;
use App\Core\Audit\Config\AuditConfig;
use App\Core\Audit\Repositories\DatabaseAuditRepository;
use App\Core\Contracts\Audit\AuditRepositoryInterface;
use App\Core\Contracts\Events\DomainEventInterface;
use App\Core\Security\SecurityContext;
use App\Core\Services\BaseService;

/**
 * Class AuditService
 *
 * Core Service penyedia layanan perekaman log audit & activity trail.
 */
class AuditService extends BaseService
{
    protected AuditRepositoryInterface $repository;
    protected AuditConfig $config;

    public function __construct(
        ?AuditRepositoryInterface $repository = null,
        ?AuditConfig $config = null
    ) {
        parent::__construct();
        $this->config = $config ?? new AuditConfig();
        $this->repository = $repository ?? new DatabaseAuditRepository();
    }

    /**
     * Menyimpan rekaman AuditEntry ke basis data.
     */
    public function record(AuditEntry $entry): bool
    {
        if (!$this->config->enabled) {
            return false;
        }

        return $this->repository->store($entry);
    }

    /**
     * Memproses konversi DomainEvent menjadi AuditEntry dan menyimpannya.
     */
    public function recordEvent(DomainEventInterface $event): bool
    {
        if (!$this->config->enabled) {
            return false;
        }

        $payload = $event->payload();
        $entityName = (string) ($payload['entity_name'] ?? 'System');
        $entityId = $payload['entity_id'] ?? ($payload['entity']->id ?? null);

        $request = service('request');

        $entry = new AuditEntry(
            event: $event->eventName(),
            entity: $entityName,
            entityId: $entityId,
            userId: SecurityContext::id(),
            ipAddress: $this->config->storeIPAddress ? (string) ($request->getIPAddress() ?? '') : '',
            userAgent: $this->config->storeUserAgent ? (string) ($request->getUserAgent()?->getAgentString() ?? '') : '',
            payload: $this->config->storePayload ? $payload : [],
            createdAt: $event->occurredAt()
        );

        return $this->record($entry);
    }

    /**
     * Mencari entri audit log berdasarkan ID.
     */
    public function find(int|string $id): ?AuditEntry
    {
        return $this->repository->find($id);
    }

    /**
     * Membaca entri audit log terpaginasi.
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginate($page, $perPage);
    }
}
