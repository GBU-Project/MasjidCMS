<?php

declare(strict_types=1);

namespace App\Domains\Financial\Events;

use App\Core\Contracts\Events\DomainEventInterface;

trait HasDomainEventsTrait
{
    /** @var DomainEventInterface[] */
    protected array $recordedDomainEvents = [];

    public function recordEvent(DomainEventInterface $event): void
    {
        $this->recordedDomainEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedDomainEvents;
        $this->recordedDomainEvents = [];
        return $events;
    }

    public function clearEvents(): void
    {
        $this->recordedDomainEvents = [];
    }

    /**
     * @return DomainEventInterface[]
     */
    public function getRecordedEvents(): array
    {
        return $this->recordedDomainEvents;
    }
}
