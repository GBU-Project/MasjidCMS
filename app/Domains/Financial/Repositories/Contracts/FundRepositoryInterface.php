<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\Fund;
use App\Domains\Financial\Entities\ValueObjects\FundCode;

interface FundRepositoryInterface
{
    public function findById(int $id): ?Fund;
    public function findByIdForUpdate(int $id): ?Fund;
    public function findByUuid(string $uuid): ?Fund;
    public function findByCode(FundCode $fundCode): ?Fund;
    public function findAllByMasjid(string $masjidId): array;
    public function save(Fund $fund): Fund;
    public function delete(Fund $fund): bool;
}
