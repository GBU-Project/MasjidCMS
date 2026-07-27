<?php

namespace App\Domains\Authorization\Services;

use App\Core\Contracts\Auth\PermissionProviderInterface;
use App\Domains\System\Entities\AuthenticatedUser;
use Config\Database;

/**
 * Class AuthorizationService
 *
 * Mengimplementasikan PermissionProviderInterface dari Core Platform.
 * Menangani evaluasi role, permission, assignment, dan tenant scoping.
 */
class AuthorizationService implements PermissionProviderInterface
{
    protected array $rolePermissionsCache = [];
    protected array $userRolesCache = [];

    public function getRoles(AuthenticatedUser $user): array
    {
        $userId = (string) $user->id;
        if (isset($this->userRolesCache[$userId])) {
            return $this->userRolesCache[$userId];
        }

        $roles = [];
        try {
            $db = Database::connect();
            if ($db->tableExists('user_roles') && $db->tableExists('roles')) {
                $rows = $db->table('user_roles')
                    ->select('roles.role_code')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('user_roles.user_id', $userId)
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    $roles[] = $row['role_code'];
                }
            }
        } catch (\Throwable $e) {
            // Memory or mock fallback
            $roles = $user->roles ?? [];
        }

        if (empty($roles) && !empty($user->roles)) {
            $roles = $user->roles;
        }

        $this->userRolesCache[$userId] = array_unique($roles);
        return $this->userRolesCache[$userId];
    }

    public function getPermissions(AuthenticatedUser $user): array
    {
        $roles = $this->getRoles($user);
        if (in_array('SUPER_ADMIN', $roles, true)) {
            return ['*'];
        }

        $permissions = [];
        try {
            $db = Database::connect();
            if ($db->tableExists('roles') && $db->tableExists('role_permissions')) {
                $rows = $db->table('roles')
                    ->select('role_permissions.permission_code')
                    ->join('role_permissions', 'role_permissions.role_id = roles.id')
                    ->whereIn('roles.role_code', $roles)
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    $permissions[] = $row['permission_code'];
                }
            }
        } catch (\Throwable $e) {
            $permissions = $user->permissions ?? [];
        }

        if (empty($permissions) && !empty($user->permissions)) {
            $permissions = $user->permissions;
        }

        return array_unique($permissions);
    }

    public function hasRole(AuthenticatedUser $user, string $role): bool
    {
        $roles = $this->getRoles($user);
        if (in_array('SUPER_ADMIN', $roles, true)) {
            return true;
        }

        return in_array(strtoupper(trim($role)), $roles, true);
    }

    public function hasPermission(AuthenticatedUser $user, string $permission): bool
    {
        $roles = $this->getRoles($user);
        if (in_array('SUPER_ADMIN', $roles, true)) {
            return true;
        }

        $permissions = $this->getPermissions($user);
        $targetPerm  = strtolower(trim($permission));

        foreach ($permissions as $perm) {
            $perm = strtolower(trim($perm));
            if ($perm === '*' || $perm === $targetPerm) {
                return true;
            }
            // Wildcard matching (e.g. 'jamaah.*' matches 'jamaah.read')
            if (str_ends_with($perm, '.*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($targetPerm, $prefix . '.')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function assignRole(string $userId, string $roleId): bool
    {
        try {
            $db = Database::connect();
            if ($db->tableExists('user_roles')) {
                $existing = $db->table('user_roles')->where('user_id', $userId)->where('role_id', $roleId)->get()->getRow();
                if (!$existing) {
                    return $db->table('user_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => $roleId,
                    ]);
                }
            }
        } catch (\Throwable $e) {
        }
        return true;
    }

    public function assignMasjidRole(string $masjidId, string $userId, string $roleId): bool
    {
        try {
            $db = Database::connect();
            if ($db->tableExists('masjid_user_roles')) {
                return $db->table('masjid_user_roles')->insert([
                    'id'         => vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)),
                    'masjid_id'  => $masjidId,
                    'user_id'    => $userId,
                    'role_id'    => $roleId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {
        }
        return true;
    }
}
