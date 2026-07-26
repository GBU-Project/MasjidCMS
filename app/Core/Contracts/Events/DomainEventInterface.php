<?php

namespace App\Core\Contracts\Events;

/**
 * Interface DomainEventInterface
 *
 * Kontrak dasar untuk seluruh Domain Event di MasjidCMS.
 */
interface DomainEventInterface
{
    /**
     * Nama unik event (misal: 'masjid.created', 'user.logged_in').
     *
     * @return string
     */
    public function eventName(): string;

    /**
     * Timestamp saat event terjadi.
     *
     * @return string
     */
    public function occurredAt(): string;

    /**
     * Data payload yang dibawa oleh event.
     *
     * @return array
     */
    public function payload(): array;
}
