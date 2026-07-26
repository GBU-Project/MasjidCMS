<?php

namespace App\Domains\System\Providers;

use App\Core\Contracts\Auth\PermissionProviderInterface;
use App\Domains\System\Config\AuthConfig;
use InvalidArgumentException;

/**
 * Class PermissionProviderFactory
 *
 * Factory pembentuk instansiasi PermissionProviderInterface berdasarkan konfigurasi aktif.
 */
class PermissionProviderFactory
{
    /**
     * Membuat instansi Permission Provider.
     *
     * @param string|null $providerName
     * @param AuthConfig|null $config
     * @return PermissionProviderInterface
     * @throws InvalidArgumentException
     */
    public static function create(?string $providerName = null, ?AuthConfig $config = null): PermissionProviderInterface
    {
        $config = $config ?? new AuthConfig();
        $provider = strtolower($providerName ?? $config->default_permission_provider ?? 'database');

        return match ($provider) {
            'database', 'db' => new DatabasePermissionProvider(),
            default          => throw new InvalidArgumentException(sprintf('Unsupported permission provider: [%s]', $provider)),
        };
    }
}
