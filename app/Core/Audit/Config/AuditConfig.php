<?php

namespace App\Core\Audit\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class AuditConfig
 *
 * Konfigurasi teknis untuk Activity Log & Audit Engine.
 */
class AuditConfig extends BaseConfig
{
    /**
     * Mengaktifkan/mematikan perekaman audit log.
     */
    public bool $enabled = true;

    /**
     * Menyimpan data payload event ke audit log.
     */
    public bool $storePayload = true;

    /**
     * Menyimpan User Agent HTTP request ke audit log.
     */
    public bool $storeUserAgent = true;

    /**
     * Menyimpan IP Address HTTP request ke audit log.
     */
    public bool $storeIPAddress = true;
}
