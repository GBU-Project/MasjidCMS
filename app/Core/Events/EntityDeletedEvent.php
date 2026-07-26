<?php

namespace App\Core\Events;

/**
 * Class EntityDeletedEvent
 *
 * Generic Event yang ditayangkan saat entitas berhasil dihapus.
 */
class EntityDeletedEvent extends AbstractDomainEvent
{
    public function __construct(string $entityName, int|string $entityId)
    {
        parent::__construct(
            eventName: strtolower($entityName) . '.deleted',
            payload: [
                'entity_name' => $entityName,
                'entity_id'   => $entityId,
            ]
        );
    }
}
