<?php

namespace App\Domains\System\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Class AuthenticationRepository
 *
 * Database Access Layer khusus penyedia identitas berbasis database local.
 */
class AuthenticationRepository extends BaseRepository
{
    protected string $table = 'users';

    /**
     * Mencari data pengguna untuk otentikasi berdasarkan username atau email.
     *
     * @param string $identifier
     * @return AuthenticatedUser|null
     */
    public function findForAuthentication(string $identifier): ?AuthenticatedUser
    {
        if (empty(trim($identifier))) {
            return null;
        }

        $row = $this->builder()
            ->groupStart()
                ->where('username', $identifier)
                ->orWhere('email', $identifier)
            ->groupEnd()
            ->get()
            ->getRowArray();

        return $this->mapToEntity($row);
    }

    /**
     * Mencari pengguna berdasarkan Primary ID.
     *
     * @param int|string $id
     * @return AuthenticatedUser|null
     */
    public function findById(int|string $id): ?AuthenticatedUser
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
     * Data mapper dari raw database row ke AuthenticatedUser Entity.
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
            id: $data['id'] ?? null,
            username: $data['username'] ?? '',
            displayName: $data['display_name'] ?? $data['username'] ?? '',
            email: $data['email'] ?? '',
            passwordHash: $data['password_hash'] ?? $data['password'] ?? null,
            roles: is_string($data['roles'] ?? null) ? json_decode($data['roles'], true) : ($data['roles'] ?? []),
            permissions: is_string($data['permissions'] ?? null) ? json_decode($data['permissions'], true) : ($data['permissions'] ?? [])
        );
    }
}
