<?php

declare(strict_types=1);

namespace App\Domains\Financial\Events;

use App\Core\Contracts\Events\DomainEventInterface;
use InvalidArgumentException;

abstract class AbstractFinancialDomainEvent implements DomainEventInterface
{
    protected string $eventId;
    protected string $aggregateId;
    protected string $aggregateType;
    protected string $eventName;
    protected string $occurredAt;
    protected int $eventVersion;
    protected array $payload;

    public function __construct(
        string $eventId,
        string $aggregateId,
        string $aggregateType,
        string $eventName,
        array $payload = [],
        int $eventVersion = 1,
        ?string $occurredAt = null
    ) {
        if (empty(trim($eventId))) {
            throw new InvalidArgumentException("Event ID tidak boleh kosong.");
        }
        if (empty(trim($aggregateId))) {
            throw new InvalidArgumentException("Aggregate ID tidak boleh kosong.");
        }

        $this->eventId = $eventId;
        $this->aggregateId = $aggregateId;
        $this->aggregateType = $aggregateType;
        $this->eventName = $eventName;
        $this->payload = $payload;
        $this->eventVersion = $eventVersion;
        $this->occurredAt = $occurredAt ?? date('Y-m-d H:i:s');
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function aggregateType(): string
    {
        return $this->aggregateType;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }

    public function eventVersion(): int
    {
        return $this->eventVersion;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
