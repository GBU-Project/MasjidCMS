<?php

namespace App\Domains\System\Providers;

use App\Core\Contracts\Auth\IdentityProviderInterface;
use App\Domains\System\Config\AuthConfig;
use InvalidArgumentException;

/**
 * Class IdentityProviderFactory
 *
 * Factory pembentuk instansiasi IdentityProviderInterface berdasarkan konfigurasi aktif.
 */
class IdentityProviderFactory
{
    /**
     * Membuat instansi Identity Provider.
     *
     * @param string|null $providerName
     * @param AuthConfig|null $config
     * @return IdentityProviderInterface
     * @throws InvalidArgumentException
     */
    public static function create(?string $providerName = null, ?AuthConfig $config = null): IdentityProviderInterface
    {
        $config = $config ?? new AuthConfig();
        $provider = strtolower($providerName ?? $config->default_provider ?? 'database');

        return match ($provider) {
            'database', 'db' => new DatabaseIdentityProvider(),
            default          => throw new InvalidArgumentException(sprintf('Unsupported identity provider: [%s]', $provider)),
        };
    }
}
