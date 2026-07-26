<?php

namespace App\Domains\Masjid\Services;

use App\Core\Exceptions\NotFoundException;
use App\Core\Services\BaseService;
use App\Domains\Masjid\DTO\CreateMasjidRequest;
use App\Domains\Masjid\DTO\UpdateMasjidRequest;
use App\Domains\Masjid\Entities\Masjid;
use App\Domains\Masjid\Repositories\MasjidRepository;

/**
 * Class MasjidService
 *
 * Business Service Layer untuk orkestrasi dan logika bisnis domain Masjid.
 */
class MasjidService extends BaseService
{
    protected MasjidRepository $repository;

    public function __construct(?MasjidRepository $repository = null)
    {
        parent::__construct();
        $this->repository = $repository ?? new MasjidRepository();
    }

    /**
     * Membuat profil masjid baru (Skeleton / Placeholder).
     */
    public function create(CreateMasjidRequest $dto): Masjid
    {
        $this->logInfo('Creating new Masjid profile: ' . $dto->name);

        return new Masjid(
            id: 1,
            code: $dto->code,
            name: $dto->name,
            slug: $dto->slug,
            address: $dto->address,
            phone: $dto->phone,
            email: $dto->email,
            website: $dto->website,
            status: $dto->status,
            createdAt: date('Y-m-d H:i:s')
        );
    }

    /**
     * Memperbarui profil masjid berdasarkan ID (Skeleton / Placeholder).
     */
    public function update(int|string $id, UpdateMasjidRequest $dto): Masjid
    {
        $existing = $this->find($id);

        if (!$existing) {
            throw new NotFoundException(sprintf('Masjid with ID [%s] not found.', (string) $id));
        }

        $this->logInfo('Updating Masjid profile ID: ' . $id);

        return new Masjid(
            id: $existing->id,
            code: $existing->code,
            name: $dto->name ?? $existing->name,
            slug: $dto->slug ?? $existing->slug,
            address: $dto->address ?? $existing->address,
            phone: $dto->phone ?? $existing->phone,
            email: $dto->email ?? $existing->email,
            website: $dto->website ?? $existing->website,
            status: $dto->status ?? $existing->status,
            createdAt: $existing->createdAt,
            updatedAt: date('Y-m-d H:i:s')
        );
    }

    /**
     * Menghapus profil masjid berdasarkan ID (Skeleton / Placeholder).
     */
    public function delete(int|string $id): bool
    {
        $existing = $this->find($id);

        if (!$existing) {
            throw new NotFoundException(sprintf('Masjid with ID [%s] not found.', (string) $id));
        }

        $this->logInfo('Deleting Masjid profile ID: ' . $id);
        return true;
    }

    /**
     * Mengembalikan data masjid yang dihapus (Restore - Placeholder).
     */
    public function restore(int|string $id): bool
    {
        $this->logInfo('Restoring Masjid profile ID: ' . $id);
        return true;
    }

    /**
     * Mencari data masjid berdasarkan ID.
     */
    public function find(int|string $id): ?Masjid
    {
        return $this->repository->find($id);
    }

    /**
     * Membaca data masjid terpaginasi.
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginateMasjid($page, $perPage);
    }

    /**
     * Public helper untuk eksekusi validasi aturan domain Masjid.
     */
    public function validateDomainRules(array $data, array $rules, array $messages = []): bool
    {
        return $this->validate($data, $rules, $messages);
    }
}
