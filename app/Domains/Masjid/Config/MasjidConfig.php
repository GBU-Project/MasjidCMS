<?php

namespace App\Domains\Masjid\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class MasjidConfig
 *
 * Object Konfigurasi teknis bawaan untuk Domain Masjid.
 */
class MasjidConfig extends BaseConfig
{
    /**
     * Jumlah item bawaan per halaman untuk pagination.
     */
    public int $default_per_page = 15;

    /**
     * Format penamaan kode unik masjid default.
     */
    public string $code_prefix = 'MSJ-';
}
