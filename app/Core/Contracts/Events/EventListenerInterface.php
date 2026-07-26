<?php

namespace App\Core\Contracts\Events;

/**
 * Interface EventListenerInterface
 *
 * Kontrak standar untuk kelas penangan (Listener) dari Domain Event.
 */
interface EventListenerInterface
{
    /**
     * Memproses / menangani domain event yang masuk.
     *
     * @param DomainEventInterface $event
     * @return void
     */
    public function handle(DomainEventInterface $event): void;
}
