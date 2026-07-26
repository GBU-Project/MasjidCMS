<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Class AuthenticationRepository
 *
 * Repository layer untuk pencarian data user/kredensial dari database.
 */
class AuthenticationRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * Mencari pengguna berdasarkan username.
     *
     * @param string $username
     * @return AuthenticatedUser|null
     */
    public function findByUsername(string $username): ?AuthenticatedUser
    {
        if (empty($username)) {
            return null;
        }

        $row = $this->builder()
            ->where('username', $username)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari pengguna berdasarkan email.
     *
     * @param string $email
     * @return AuthenticatedUser|null
     */
    public function findByEmail(string $email): ?AuthenticatedUser
    {
        if (empty($email)) {
            return null;
        }

        $row = $this->builder()
            ->where('email', $email)
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari pengguna berdasarkan identitas (bisa username atau email).
     *
     * @param string $identity
     * @return AuthenticatedUser|null
     */
    public function findByCredential(string $identity): ?AuthenticatedUser
    {
        if (empty($identity)) {
            return null;
        }

        $row = $this->builder()
            ->groupStart()
                ->where('username', $identity)
                ->orWhere('email', $identity)
            ->groupEnd()
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Data mapper merubah raw database array ke AuthenticatedUser Entity.
     *
     * @param array|null $data
     * @return AuthenticatedUser|null
     */
    protected function mapToEntity(?array $data): ?AuthenticatedUser
    {
        if (empty($data)) {
            return null;
        }

        return new AuthenticatedUser(
            $data['id'] ?? null,
            $data['username'] ?? '',
            $data['display_name'] ?? $data['username'] ?? '',
            $data['email'] ?? '',
            $data['password_hash'] ?? $data['password'] ?? null,
            is_string($data['roles'] ?? null) ? json_decode($data['roles'], true) : ($data['roles'] ?? []),
            is_string($data['permissions'] ?? null) ? json_decode($data['permissions'], true) : ($data['permissions'] ?? [])
        );
    }
}
