<?php

namespace App\Domains\System\Services;

use App\Core\Exceptions\AuthorizationException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\BaseService;
use App\Domains\System\DTO\LoginRequest;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Events\AuthenticationEvent;
use App\Domains\System\Repositories\AuthenticationRepository;

/**
 * Class AuthenticationService
 *
 * Business Orchestration layer untuk proses otentikasi pengguna di MasjidCMS.
 * Menggunakan Dependency Injection dan terpisah dari Session/Database/HTTP direct access.
 */
class AuthenticationService extends BaseService
{
    protected AuthenticationRepository $repository;
    protected PasswordService $passwordService;
    protected SessionService $sessionService;
    protected AuthenticationEvent $event;

    public function __construct(
        ?AuthenticationRepository $repository = null,
        ?PasswordService $passwordService = null,
        ?SessionService $sessionService = null,
        ?AuthenticationEvent $event = null
    ) {
        parent::__construct();
        $this->repository = $repository ?? new AuthenticationRepository();
        $this->passwordService = $passwordService ?? new PasswordService();
        $this->sessionService = $sessionService ?? new SessionService();
        $this->event = $event ?? new AuthenticationEvent();
    }

    /**
     * Memproses alur login pengguna.
     *
     * @param LoginRequest $request
     * @return AuthenticatedUser
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function login(LoginRequest $request): AuthenticatedUser
    {
        // 1. Validasi DTO Input
        if (empty(trim($request->username)) || empty(trim($request->password))) {
            throw new ValidationException('Validation failed', [
                'username' => 'Username/Email is required.',
                'password' => 'Password is required.',
            ]);
        }

        // 2. Cari pengguna via Repository (Tanpa SQL di Service)
        $user = $this->repository->findByCredential(trim($request->username));

        if (!$user || empty($user->passwordHash)) {
            $this->logError(sprintf('Failed login attempt for username: %s', $request->username));
            throw new AuthorizationException('Invalid username or password.');
        }

        // 3. Verifikasi Password via PasswordService
        if (!$this->passwordService->verify($request->password, $user->passwordHash)) {
            $this->logError(sprintf('Invalid password attempt for username: %s', $request->username));
            throw new AuthorizationException('Invalid username or password.');
        }

        // 4. Lakukan Session Login via SessionService (Tanpa Session direct di Service)
        $this->sessionService->login($user, $request->remember);

        // 5. Trigger Event Audit Log Hook
        $this->event->onLogin($user);

        return $user;
    }

    /**
     * Memproses logout pengguna.
     *
     * @return bool
     */
    public function logout(): bool
    {
        $user = $this->currentUser();
        $this->sessionService->logout();
        $this->event->onLogout($user);

        return true;
    }

    /**
     * Memeriksa kredensial tanpa membuat sesi.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateCredential(string $username, string $password): bool
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        $user = $this->repository->findByCredential($username);

        if (!$user || empty($user->passwordHash)) {
            return false;
        }

        return $this->passwordService->verify($password, $user->passwordHash);
    }

    /**
     * Memperbarui Session ID.
     *
     * @return bool
     */
    public function refresh(): bool
    {
        $this->sessionService->regenerate();
        return true;
    }

    /**
     * Mendapatkan user terotentikasi saat ini.
     *
     * @return AuthenticatedUser|null
     */
    public function currentUser(): ?AuthenticatedUser
    {
        return $this->sessionService->currentUser();
    }

    /**
     * Memeriksa status login user.
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->sessionService->check();
    }

    /**
     * Memeriksa status guest user.
     *
     * @return bool
     */
    public function guest(): bool
    {
        return $this->sessionService->guest();
    }
}
