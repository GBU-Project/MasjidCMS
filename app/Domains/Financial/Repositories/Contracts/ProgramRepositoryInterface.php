<?php

declare(strict_types=1);

namespace App\Domains\Financial\Repositories\Contracts;

use App\Domains\Financial\Entities\Program;
use App\Domains\Financial\Entities\ValueObjects\ProgramCode;

interface ProgramRepositoryInterface
{
    public function findById(int $id): ?Program;
    public function findByUuid(string $uuid): ?Program;
    public function findByCode(ProgramCode $programCode): ?Program;
    public function save(Program $program): Program;
}
