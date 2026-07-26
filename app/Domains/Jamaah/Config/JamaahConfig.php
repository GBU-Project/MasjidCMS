<?php

namespace App\Domains\Jamaah\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class JamaahConfig
 *
 * Konfigurasi khusus modul Domain Jamaah.
 */
class JamaahConfig extends BaseConfig
{
    public bool $enabled = true;
    public string $domainName = 'Jamaah';
}
