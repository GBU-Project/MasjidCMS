<?php

namespace App\Domains\System\Services;

use App\Core\Contracts\Auth\PermissionProviderInterface;
use App\Core\Exceptions\AuthorizationException;
use App\Core\Services\BaseService;
use App\Domains\System\Config\AuthConfig;
use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Providers\PermissionProviderFactory;

/**
 * Class RBACService
 *
 * Core Service penyedia otorisasi berbasis peran (Role-Based Access Control).
 * Berinteraksi secara decoupled dengan PermissionProviderInterface.
 */
class RBACService extends BaseService
{
    protected PermissionProviderInterface $permissionProvider;
    protected AuthConfig $config;

    public function __construct(
        ?PermissionProviderInterface $permissionProvider = null,
        ?AuthConfig $config = null
    ) {
        parent::__construct();
        $this->config = $config ?? new AuthConfig();
        $this->permissionProvider = $permissionProvider ?? PermissionProviderFactory::create($this->config->default_permission_provider, $this->config);
    }

    /**
     * Mendapatkan daftar role pengguna via PermissionProviderInterface.
     *
     * @param AuthenticatedUser $user
     * @return array<string>
     */
    public function roles(AuthenticatedUser $user): array
    {
        return $this->permissionProvider->getRoles($user);
    }

    /**
     * Mendapatkan daftar permission pengguna via PermissionProviderInterface.
     *
     * @param AuthenticatedUser $user
     * @return array<string>
     */
    public function permissions(AuthenticatedUser $user): array
    {
        return $this->permissionProvider->getPermissions($user);
    }

    /**
     * Memeriksa apakah pengguna memiliki role spesifik.
     *
     * @param AuthenticatedUser $user
     * @param string $role
     * @return bool
     */
    public function hasRole(AuthenticatedUser $user, string $role): bool
    {
        return $this->permissionProvider->hasRole($user, $role);
    }

    /**
     * Memeriksa apakah pengguna memiliki permission spesifik.
     *
     * @param AuthenticatedUser $user
     * @param string $permission
     * @return bool
     */
    public function hasPermission(AuthenticatedUser $user, string $permission): bool
    {
        return $this->permissionProvider->hasPermission($user, $permission);
    }

    /**
     * Memeriksa apakah pengguna adalah Super Admin.
     *
     * @param AuthenticatedUser $user
     * @return bool
     */
    public function isSuperAdmin(AuthenticatedUser $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Memastikan pengguna memiliki hak akses (permission). Melempar AuthorizationException jika gagal.
     *
     * @param AuthenticatedUser $user
     * @param string $permission
     * @return bool
     * @throws AuthorizationException
     */
    public function authorize(AuthenticatedUser $user, string $permission): bool
    {
        if (!$this->hasPermission($user, $permission)) {
            $this->logError(sprintf('Access denied for user [%s] on permission [%s]', $user->username, $permission));
            throw new AuthorizationException(sprintf('User does not have required permission [%s].', $permission));
        }

        return true;
    }
}
