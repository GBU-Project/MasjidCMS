<?php

namespace App\Domains\Masjid\Policies;

use App\Domains\System\Entities\AuthenticatedUser;
use App\Domains\System\Services\RBACService;

/**
 * Class MasjidPolicy
 *
 * Policy layer penentu hak akses spesifik untuk sumber daya Domain Masjid.
 */
class MasjidPolicy
{
    protected RBACService $rbacService;

    public function __construct(?RBACService $rbacService = null)
    {
        $this->rbacService = $rbacService ?? new RBACService();
    }

    /**
     * Pengecekan hak akses melihat profil masjid.
     */
    public function view(AuthenticatedUser $user): bool
    {
        return $this->rbacService->hasPermission($user, 'masjid.view');
    }

    /**
     * Pengecekan hak akses membuat profil masjid.
     */
    public function create(AuthenticatedUser $user): bool
    {
        return $this->rbacService->hasPermission($user, 'masjid.create');
    }

    /**
     * Pengecekan hak akses memperbarui profil masjid.
     */
    public function update(AuthenticatedUser $user): bool
    {
        return $this->rbacService->hasPermission($user, 'masjid.update');
    }

    /**
     * Pengecekan hak akses menghapus profil masjid.
     */
    public function delete(AuthenticatedUser $user): bool
    {
        return $this->rbacService->hasPermission($user, 'masjid.delete');
    }

    /**
     * Pengecekan hak akses restore profil masjid.
     */
    public function restore(AuthenticatedUser $user): bool
    {
        return $this->rbacService->hasPermission($user, 'masjid.restore');
    }
}
