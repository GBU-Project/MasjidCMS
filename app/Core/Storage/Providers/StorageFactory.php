<?php

namespace App\Core\Storage\Providers;

use App\Core\Contracts\Storage\StorageProviderInterface;
use App\Core\Storage\Config\StorageConfig;
use InvalidArgumentException;

/**
 * Class StorageFactory
 *
 * Factory pembentuk instansiasi StorageProviderInterface berdasarkan konfigurasi aktif.
 */
class StorageFactory
{
    /**
     * Membuat instansi Storage Provider.
     *
     * @param string|null $providerName
     * @param StorageConfig|null $config
     * @return StorageProviderInterface
     * @throws InvalidArgumentException
     */
    public static function create(?string $providerName = null, ?StorageConfig $config = null): StorageProviderInterface
    {
        $config = $config ?? new StorageConfig();
        $provider = strtolower($providerName ?? $config->default_provider ?? 'local');

        return match ($provider) {
            'local', 'filesystem' => new LocalStorageProvider($config),
            default               => throw new InvalidArgumentException(sprintf('Unsupported storage provider: [%s]', $provider)),
        };
    }
}
