<?php

namespace App\Domains\System\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class AuthConfig
 *
 * Konfigurasi default untuk modul Otentikasi pada Domain System.
 */
class AuthConfig extends BaseConfig
{
    /**
     * Waktu kadaluarsa sesi (dalam detik).
     *
     * @var int
     */
    public int $session_timeout = 7200;

    /**
     * Mengaktifkan/mematikan fitur Remember Me.
     *
     * @var bool
     */
    public bool $remember_me = true;

    /**
     * Jumlah maksimum percobaan login sebelum diproteksi.
     *
     * @var int
     */
    public int $max_login_attempt = 5;

    /**
     * Durasi kuncian account (dalam menit) setelah batas percobaan login terlampaui.
     *
     * @var int
     */
    public int $lockout_minutes = 15;

    /**
     * Algoritma hashing password bawaan.
     *
     * @var string
     */
    public string $password_algorithm = 'bcrypt';
}
