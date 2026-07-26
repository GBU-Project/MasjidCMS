<?php

namespace App\Core\Events;

use App\Core\Contracts\Events\DomainEventInterface;

/**
 * Class AbstractDomainEvent
 *
 * Kelas abstrak induk penampung properti umum (eventName, timestamp, payload) untuk Domain Event.
 */
abstract class AbstractDomainEvent implements DomainEventInterface
{
    protected string $eventName;
    protected string $occurredAt;
    protected array $payload;

    public function __construct(string $eventName, array $payload = [])
    {
        $this->eventName = $eventName;
        $this->payload = $payload;
        $this->occurredAt = date('Y-m-d H:i:s');
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
