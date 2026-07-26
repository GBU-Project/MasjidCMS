<?php

namespace App\Domains\System\Providers;

use App\Core\Contracts\Auth\IdentityProviderInterface;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Repositories\AuthenticationRepository;
use App\Domains\System\Services\PasswordService;

/**
 * Class DatabaseIdentityProvider
 *
 * Implementasi Identity Provider berbasis Database lokal MasjidCMS.
 * DILARANG berinteraksi dengan Session secara langsung.
 */
class DatabaseIdentityProvider implements IdentityProviderInterface
{
    protected AuthenticationRepository $repository;
    protected PasswordService $passwordService;

    public function __construct(
        ?AuthenticationRepository $repository = null,
        ?PasswordService $passwordService = null
    ) {
        $this->repository = $repository ?? new AuthenticationRepository();
        $this->passwordService = $passwordService ?? new PasswordService();
    }

    /**
     * Mencari pengguna berdasarkan username atau email via Repository.
     */
    public function findByIdentifier(string $identifier): ?AuthenticatedUser
    {
        return $this->repository->findForAuthentication($identifier);
    }

    /**
     * Mencari pengguna berdasarkan ID via Repository.
     */
    public function findById(int|string $id): ?AuthenticatedUser
    {
        return $this->repository->findById($id);
    }

    /**
     * Memverifikasi password pengguna via PasswordService.
     */
    public function validateCredential(AuthenticatedUser $user, string $password): bool
    {
        if (empty($user->passwordHash) || empty($password)) {
            return false;
        }

        return $this->passwordService->verify($password, $user->passwordHash);
    }

    /**
     * Database provider mendukung Remember Me.
     */
    public function supportsRememberMe(): bool
    {
        return true;
    }

    /**
     * Database provider mendukung perubahan password secara internal.
     */
    public function supportsPasswordChange(): bool
    {
        return true;
    }
}
