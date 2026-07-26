<?php

namespace App\Domains\System\Services;

use App\Core\Services\BaseService;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\DTO\LoginRequest;
use LogicException;

/**
 * Class AuthenticationService
 *
 * Service skeleton untuk pengelolaan otentikasi pengguna pada Domain System.
 */
class AuthenticationService extends BaseService
{
    /**
     * Melakukan proses otentikasi (login).
     *
     * @param LoginRequest $request
     * @return AuthenticatedUser
     * @throws LogicException
     */
    public function login(LoginRequest $request): AuthenticatedUser
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Melakukan proses pengakhiran sesi (logout).
     *
     * @return bool
     * @throws LogicException
     */
    public function logout(): bool
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Memperbarui/refresh token atau sesi otentikasi.
     *
     * @return bool
     * @throws LogicException
     */
    public function refresh(): bool
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Memvalidasi kredensial pengguna.
     *
     * @param string $username
     * @param string $password
     * @return bool
     * @throws LogicException
     */
    public function validateCredential(string $username, string $password): bool
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Mendapatkan entitas pengguna yang sedang aktif/login.
     *
     * @return AuthenticatedUser|null
     * @throws LogicException
     */
    public function currentUser(): ?AuthenticatedUser
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Memeriksa apakah pengguna saat ini telah terotentikasi.
     *
     * @return bool
     * @throws LogicException
     */
    public function check(): bool
    {
        throw new LogicException('Not implemented.');
    }

    /**
     * Memeriksa apakah pengguna saat ini adalah guest (belum login).
     *
     * @return bool
     * @throws LogicException
     */
    public function guest(): bool
    {
        throw new LogicException('Not implemented.');
    }
}
