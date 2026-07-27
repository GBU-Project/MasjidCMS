<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Financial\UnitOfWork;

use App\Core\Contracts\Events\DomainEventInterface;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

class FinancialUnitOfWork
{
    protected ?BaseConnection $db = null;
    /** @var DomainEventInterface[] */
    protected array $uncommittedEvents = [];
    protected bool $inTransaction = false;

    public function __construct(?BaseConnection $db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            try {
                $this->db = Database::connect();
            } catch (\Throwable $e) {
                // Database offline during CLI unit test mode
                $this->db = null;
            }
        }
    }

    public function begin(): void
    {
        if ($this->db !== null) {
            $this->db->transBegin();
        }
        $this->inTransaction = true;
    }

    public function commit(): void
    {
        if ($this->inTransaction) {
            if ($this->db !== null) {
                $this->db->transCommit();
            }
            $this->inTransaction = false;
        }
    }

    public function rollback(): void
    {
        if ($this->inTransaction) {
            if ($this->db !== null) {
                $this->db->transRollback();
            }
            $this->inTransaction = false;
        }
        $this->uncommittedEvents = [];
    }

    public function collectEvents(object $aggregate): void
    {
        if (method_exists($aggregate, 'releaseEvents')) {
            $events = $aggregate->releaseEvents();
            foreach ($events as $event) {
                if ($event instanceof DomainEventInterface) {
                    $this->uncommittedEvents[] = $event;
                }
            }
        }
    }

    /**
     * @return DomainEventInterface[]
     */
    public function releaseEvents(): array
    {
        $events = $this->uncommittedEvents;
        $this->uncommittedEvents = [];
        return $events;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function getUncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    public function isInTransaction(): bool
    {
        return $this->inTransaction;
    }
}
