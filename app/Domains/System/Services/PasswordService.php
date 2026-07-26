<?php

namespace App\Domains\System\Services;

use App\Core\Services\BaseService;
use App\Domains\System\Config\AuthConfig;

/**
 * Class PasswordService
 *
 * Encapsulation service khusus pengelolaan enkripsi, verifikasi, dan rehashing password.
 */
class PasswordService extends BaseService
{
    protected AuthConfig $config;

    public function __construct(?AuthConfig $config = null)
    {
        parent::__construct();
        $this->config = $config ?? new AuthConfig();
    }

    /**
     * Membuat hash aman dari plaintext password menggunakan BCRYPT.
     *
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        return password_hash($password, $this->config->password_algorithm, [
            'cost' => $this->config->password_cost,
        ]);
    }

    /**
     * Memverifikasi kecocokan plaintext password dengan hash terenkripsi.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {
        if (empty($hash) || empty($password)) {
            return false;
        }

        return password_verify($password, $hash);
    }

    /**
     * Memeriksa apakah hash password membutuhkan rehash (karena perubahan cost/algoritma).
     *
     * @param string $hash
     * @return bool
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->config->password_algorithm, [
            'cost' => $this->config->password_cost,
        ]);
    }

    /**
     * Membuat temporary/random password acak untuk keperluan reset.
     *
     * @param int $length
     * @return string
     */
    public function generateTemporaryPassword(int $length = 12): string
    {
        $bytes = random_bytes((int) ceil($length / 2));
        return substr(bin2hex($bytes), 0, $length);
    }
}
