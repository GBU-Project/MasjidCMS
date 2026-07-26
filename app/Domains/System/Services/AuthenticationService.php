<?php

namespace App\Domains\System\Services;

use App\Core\Contracts\Auth\IdentityProviderInterface;
use App\Core\Exceptions\AuthorizationException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\BaseService;
use App\Domains\System\Config\AuthConfig;
use App\Domains\System\DTO\LoginRequest;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Events\AuthenticationEvent;
use App\Domains\System\Providers\IdentityProviderFactory;

/**
 * Class AuthenticationService
 *
 * Business Orchestration layer untuk proses otentikasi pengguna di MasjidCMS.
 * Hanya bergantung pada IdentityProviderInterface (Decoupled Identity Provider Pattern).
 */
class AuthenticationService extends BaseService
{
    protected IdentityProviderInterface $identityProvider;
    protected SessionService $sessionService;
    protected AuthenticationEvent $event;
    protected AuthConfig $config;

    public function __construct(
        ?IdentityProviderInterface $identityProvider = null,
        ?SessionService $sessionService = null,
        ?AuthenticationEvent $event = null,
        ?AuthConfig $config = null
    ) {
        parent::__construct();
        $this->config = $config ?? new AuthConfig();
        $this->identityProvider = $identityProvider ?? IdentityProviderFactory::create($this->config->default_provider, $this->config);
        $this->sessionService = $sessionService ?? new SessionService(null, $this->config);
        $this->event = $event ?? new AuthenticationEvent();
    }

    /**
     * Memproses alur login pengguna via IdentityProviderInterface.
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

        // 2. Cari pengguna via IdentityProviderInterface
        $user = $this->identityProvider->findByIdentifier(trim($request->username));

        if (!$user) {
            $this->logError(sprintf('Failed login attempt (user not found) for: %s', $request->username));
            throw new AuthorizationException('Invalid username or password.');
        }

        // 3. Memverifikasi kredensial via IdentityProviderInterface
        if (!$this->identityProvider->validateCredential($user, $request->password)) {
            $this->logError(sprintf('Invalid credential attempt for: %s', $request->username));
            throw new AuthorizationException('Invalid username or password.');
        }

        // 4. Memeriksa dukungan remember me pada provider
        $shouldRemember = $request->remember && $this->identityProvider->supportsRememberMe();

        // 5. Simpan ke sesi via SessionService
        $this->sessionService->login($user, $shouldRemember);

        // 6. Trigger Event Audit Log Hook
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
     * Memeriksa kredensial tanpa membuat sesi via IdentityProviderInterface.
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

        $user = $this->identityProvider->findByIdentifier($username);

        if (!$user) {
            return false;
        }

        return $this->identityProvider->validateCredential($user, $password);
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
        return !$this->check();
    }
}
