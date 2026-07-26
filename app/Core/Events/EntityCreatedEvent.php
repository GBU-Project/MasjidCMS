<?php

namespace App\Core\Events;

/**
 * Class EntityCreatedEvent
 *
 * Generic Event yang ditayangkan saat entitas baru berhasil dibuat.
 */
class EntityCreatedEvent extends AbstractDomainEvent
{
    public function __construct(string $entityName, mixed $entity)
    {
        parent::__construct(
            eventName: strtolower($entityName) . '.created',
            payload: [
                'entity_name' => $entityName,
                'entity'      => $entity,
            ]
        );
    }
}
