<?php

namespace App\Domains\System\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class AuthConfig
 *
 * Konfigurasi teknis untuk Authentication Engine, Identity Provider, dan RBAC Permission Provider.
 */
class AuthConfig extends BaseConfig
{
    /**
     * Identity Provider default yang aktif ('database', 'ldap', 'oauth', dll).
     */
    public string $default_provider = 'database';

    /**
     * Permission Provider default yang aktif ('database', 'config', 'external', dll).
     */
    public string $default_permission_provider = 'database';

    /**
     * Algoritma hashing password bawaan.
     */
    public string $password_algorithm = PASSWORD_BCRYPT;

    /**
     * Cost factor untuk algoritma BCRYPT hashing.
     */
    public int $password_cost = 10;

    /**
     * Durasi Remember Me (dalam detik) - Default 30 hari.
     */
    public int $remember_duration = 2592000;

    /**
     * Maksimum percobaan login gagal sebelum akun terkunci sementara.
     */
    public int $max_login_attempt = 5;

    /**
     * Durasi kuncian lockout (dalam detik) - Default 15 menit.
     */
    public int $lockout_duration = 900;

    /**
     * Waktu kadaluarsa sesi normal (dalam detik) - Default 2 jam.
     */
    public int $session_timeout = 7200;

    /**
     * Key prefix untuk menyimpan data user di CI4 Session.
     */
    public string $session_user_key = 'auth_user';

    /**
     * Key prefix untuk cookie Remember Me.
     */
    public string $remember_cookie_key = 'remember_token';
}
