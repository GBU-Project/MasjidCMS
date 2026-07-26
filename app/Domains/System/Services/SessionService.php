<?php

namespace App\Domains\System\Services;

use App\Core\Services\BaseService;
use App\Domains\System\Config\AuthConfig;
use App\Domains\System\Entities\AuthenticatedUser;
use CodeIgniter\Session\Session;
use Config\Services;

/**
 * Class SessionService
 *
 * Satu-satunya wrapper service yang diperbolehkan berinteraksi langsung dengan CodeIgniter Session.
 */
class SessionService extends BaseService
{
    protected Session $session;
    protected AuthConfig $config;

    public function __construct(?Session $session = null, ?AuthConfig $config = null)
    {
        parent::__construct();
        $this->session = $session ?? Services::session();
        $this->config = $config ?? new AuthConfig();
    }

    /**
     * Menyimpan data entitas user ke dalam sesi aktif dan melakukan regenerate session ID.
     */
    public function login(AuthenticatedUser $user, bool $remember = false): void
    {
        $this->regenerate();

        $userData = [
            'id'          => $user->id,
            'username'    => $user->username,
            'displayName' => $user->displayName(),
            'email'       => $user->email,
            'roles'       => $user->roles,
            'permissions' => $user->permissions,
        ];

        $this->session->set($this->config->session_user_key, $userData);

        if ($remember) {
            $this->remember($user);
        }
    }

    /**
     * Menghapus sesi otentikasi user dan menghancurkan session data.
     */
    public function logout(): void
    {
        $this->session->remove($this->config->session_user_key);
        $this->forgetRemember();
        $this->session->destroy();
    }

    /**
     * Memperbarui Session ID untuk mencegah Session Fixation Attack.
     */
    public function regenerate(): void
    {
        $this->session->regenerate(true);
    }

    /**
     * Mendapatkan entitas pengguna terotentikasi dari sesi saat ini.
     */
    public function currentUser(): ?AuthenticatedUser
    {
        $userData = $this->session->get($this->config->session_user_key);

        if (empty($userData) || !is_array($userData)) {
            return null;
        }

        return new AuthenticatedUser(
            $userData['id'] ?? null,
            $userData['username'] ?? '',
            $userData['displayName'] ?? '',
            $userData['email'] ?? '',
            null, // Do not store password hash in session
            $userData['roles'] ?? [],
            $userData['permissions'] ?? []
        );
    }

    /**
     * Mengatur penanda Remember Me (Cookie placeholder).
     */
    public function remember(AuthenticatedUser $user): void
    {
        // Skeleton Remember Me cookie handler
        helper('cookie');
        set_cookie(
            $this->config->remember_cookie_key,
            base64_encode((string) $user->id),
            $this->config->remember_duration
        );
    }

    /**
     * Menghapus penanda Remember Me.
     */
    public function forgetRemember(): void
    {
        helper('cookie');
        delete_cookie($this->config->remember_cookie_key);
    }

    /**
     * Memeriksa apakah user sedang terotentikasi di sesi.
     */
    public function check(): bool
    {
        return $this->currentUser() !== null;
    }

    /**
     * Memeriksa apakah user adalah guest (belum login).
     */
    public function guest(): bool
    {
        return !$this->check();
    }
}
