<?php

namespace App\Domains\Masjid\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\Masjid\Entities\Masjid;

/**
 * Class MasjidRepository
 *
 * Repository Layer untuk mengabstraksi query data profil Masjid dari database.
 */
class MasjidRepository extends BaseRepository
{
    protected string $table = 'masjid_profiles';

    /**
     * Mencari profil masjid berdasarkan Primary ID.
     */
    public function find(int|string $id): ?Masjid
    {
        if (empty($id)) {
            return null;
        }

        $row = $this->builder()
            ->where('id', $id)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari profil masjid berdasarkan Slug URL.
     */
    public function findBySlug(string $slug): ?Masjid
    {
        if (empty($slug)) {
            return null;
        }

        $row = $this->builder()
            ->where('slug', $slug)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari profil masjid berdasarkan Kode Unik.
     */
    public function findByCode(string $code): ?Masjid
    {
        if (empty($code)) {
            return null;
        }

        $row = $this->builder()
            ->where('code', $code)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Memeriksa keberadaan data masjid berdasarkan ID.
     */
    public function exists(int|string $id): bool
    {
        if (empty($id)) {
            return false;
        }

        return $this->builder()
            ->where('id', $id)
            ->countAllResults() > 0;
    }

    /**
     * Membaca daftar data profil masjid dengan pagination bawaan BaseRepository.
     */
    public function paginateMasjid(int $page = 1, int $perPage = 15): array
    {
        $builder = $this->builder();
        $result = $this->paginate($builder, $page, $perPage);

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
     * Data mapper merubah raw array ke Masjid Entity.
     */
    protected function mapToEntity(?array $data): ?Masjid
    {
        if (empty($data)) {
            return null;
        }

        return new Masjid(
            id: $data['id'] ?? null,
            code: $data['code'] ?? '',
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            address: $data['address'] ?? '',
            phone: $data['phone'] ?? '',
            email: $data['email'] ?? '',
            website: $data['website'] ?? '',
            status: $data['status'] ?? 'active',
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
