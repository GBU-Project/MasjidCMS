<?php

namespace App\Core\Contracts\Events;

/**
 * Interface EventDispatcherInterface
 *
 * Kontrak manajerial untuk pendaftaran listener dan pendistribusian (dispatch) Domain Event.
 */
interface EventDispatcherInterface
{
    /**
     * Mempublikasikan / menembakkan event ke seluruh listener terdaftar.
     *
     * @param DomainEventInterface $event
     * @return void
     */
    public function dispatch(DomainEventInterface $event): void;

    /**
     * Mendaftarkan listener untuk event tertentu.
     *
     * @param string $eventName Nama event atau '*' untuk wildcard
     * @param EventListenerInterface|callable $listener
     * @return void
     */
    public function listen(string $eventName, EventListenerInterface|callable $listener): void;

    /**
     * Menghapus listener terdaftar dari event tertentu.
     *
     * @param string $eventName
     * @return void
     */
    public function forget(string $eventName): void;

    /**
     * Memeriksa apakah event tertentu memiliki listener terdaftar.
     *
     * @param string $eventName
     * @return bool
     */
    public function hasListener(string $eventName): bool;
}
