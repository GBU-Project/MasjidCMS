<?php

namespace App\Core\Events;

use App\Core\Contracts\Events\DomainEventInterface;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Events\EventListenerInterface;

/**
 * Class EventDispatcher
 *
 * Implementasi In-Memory Event Dispatcher untuk mendistribusikan Domain Event secara sinkron dalam satu HTTP Request.
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * Peta simpanan listener per nama event.
     *
     * @var array<string, array<EventListenerInterface|callable>>
     */
    protected array $listeners = [];

    /**
     * Mempublikasikan event ke seluruh listener terdaftar.
     */
    public function dispatch(DomainEventInterface $event): void
    {
        $name = $event->eventName();

        // 1. Eksekusi listener spesifik
        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listener) {
                $this->executeListener($listener, $event);
            }
        }

        // 2. Eksekusi listener wildcard '*'
        if (isset($this->listeners['*'])) {
            foreach ($this->listeners['*'] as $listener) {
                $this->executeListener($listener, $event);
            }
        }
    }

    /**
     * Mendaftarkan listener baru.
     */
    public function listen(string $eventName, EventListenerInterface|callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * Menghapus listener event tertentu.
     */
    public function forget(string $eventName): void
    {
        unset($this->listeners[$eventName]);
    }

    /**
     * Memeriksa keberadaan listener.
     */
    public function hasListener(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Eksekutor pemanggilan listener.
     */
    protected function executeListener(EventListenerInterface|callable $listener, DomainEventInterface $event): void
    {
        if ($listener instanceof EventListenerInterface) {
            $listener->handle($event);
        } elseif (is_callable($listener)) {
            $listener($event);
        }
    }
}
