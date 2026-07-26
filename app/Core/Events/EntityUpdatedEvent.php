<?php

namespace App\Core\Events;

/**
 * Class EntityUpdatedEvent
 *
 * Generic Event yang ditayangkan saat entitas berhasil diperbarui.
 */
class EntityUpdatedEvent extends AbstractDomainEvent
{
    public function __construct(string $entityName, mixed $entity)
    {
        parent::__construct(
            eventName: strtolower($entityName) . '.updated',
            payload: [
                'entity_name' => $entityName,
                'entity'      => $entity,
            ]
        );
    }
}
