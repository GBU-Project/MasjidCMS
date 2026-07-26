<?php

namespace App\Core\Audit;

/**
 * Class AuditEntry
 *
 * Entity representasi rekaman audit trail / activity log.
 */
class AuditEntry
{
    public function __construct(
        public readonly int|string|null $id = null,
        public readonly string $event = '',
        public readonly string $entity = '',
        public readonly int|string|null $entityId = null,
        public readonly int|string|null $userId = null,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = '',
        public readonly array $payload = [],
        public readonly ?string $createdAt = null
    ) {}
}
