<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\CoaAccount;
use App\Domains\Financial\Entities\ValueObjects\AccountCode;

interface CoaAccountRepositoryInterface
{
    public function findById(int $id): ?CoaAccount;
    public function findByUuid(string $uuid): ?CoaAccount;
    public function findByCode(AccountCode $accountCode): ?CoaAccount;
    public function findAllByMasjid(string $masjidId): array;
    public function save(CoaAccount $account): CoaAccount;
}
