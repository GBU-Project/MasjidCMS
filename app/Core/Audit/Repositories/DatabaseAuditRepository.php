<?php

namespace App\Core\Audit\Repositories;

use App\Core\Audit\AuditEntry;
use App\Core\Contracts\Audit\AuditRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * Class DatabaseAuditRepository
 *
 * Repository Layer untuk penyimpan dan pembaca data log audit dari tabel `activity_logs`.
 */
class DatabaseAuditRepository extends BaseRepository implements AuditRepositoryInterface
{
    protected string $table = 'activity_logs';

    /**
     * Menyimpan log entri audit ke database.
     */
    public function store(AuditEntry $entry): bool
    {
        $data = [
            'event'      => $entry->event,
            'entity'     => $entry->entity,
            'entity_id'  => $entry->entityId,
            'user_id'    => $entry->userId,
            'ip_address' => $entry->ipAddress,
            'user_agent' => $entry->userAgent,
            'payload'    => json_encode($entry->payload),
            'created_at' => $entry->createdAt ?? date('Y-m-d H:i:s'),
        ];

        return (bool) $this->create($data);
    }

    /**
     * Mencari entri audit berdasarkan Primary ID.
     */
    public function find(int|string $id): ?AuditEntry
    {
        $row = parent::find($id);
        return $this->mapToEntity($row);
    }

    /**
     * Membaca log audit terpaginasi.
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $result = parent::paginate($page, $perPage);
        $entities = [];

        foreach ($result['data'] as $row) {
            $mapped = $this->mapToEntity($row);
            if ($mapped) {
                $entities[] = $mapped;
            }
        }

        $result['data'] = $entities;
        return $result;
    }

    /**
     * Data mapper merubah raw array DB ke AuditEntry Entity.
     */
    protected function mapToEntity(?array $data): ?AuditEntry
    {
        if (empty($data)) {
            return null;
        }

        return new AuditEntry(
            id: $data['id'] ?? null,
            event: $data['event'] ?? '',
            entity: $data['entity'] ?? '',
            entityId: $data['entity_id'] ?? null,
            userId: $data['user_id'] ?? null,
            ipAddress: $data['ip_address'] ?? '',
            userAgent: $data['user_agent'] ?? '',
            payload: is_string($data['payload'] ?? null) ? json_decode($data['payload'], true) ?? [] : ($data['payload'] ?? []),
            createdAt: $data['created_at'] ?? null
        );
    }
}
