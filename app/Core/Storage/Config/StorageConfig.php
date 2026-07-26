<?php

namespace App\Core\Storage\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class StorageConfig
 *
 * Konfigurasi teknis untuk Media Storage Engine.
 */
class StorageConfig extends BaseConfig
{
    /**
     * Driver storage default yang aktif ('local', 's3', 'minio', dll).
     */
    public string $default_provider = 'local';

    /**
     * Absolute base directory path penyimpanan lokal.
     */
    public string $base_path = ROOTPATH . 'storage/';

    /**
     * Path direktori upload publik (web root).
     */
    public string $public_path = FCPATH . 'uploads/';

    /**
     * Mengaktifkan/mematikan dukungan temporary signed URL.
     */
    public bool $temporary_url_enabled = true;
}
