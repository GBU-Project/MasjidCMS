<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;

/**
 * Class AuthenticationRepository
 *
 * Repository skeleton untuk Domain System (Authentication).
 * Bertanggung jawab atas query akses data user/credential tanpa memuat logic bisnis.
 */
class AuthenticationRepository extends BaseRepository
{
    /**
     * @var string
     */
    protected string $table = 'users';
}
