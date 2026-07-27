<?php

namespace App\Domains\Authorization\Services;

use App\Domains\System\Entities\AuthenticatedUser;

/**
 * Class PolicyResolver
 *
 * Engine penilai kebijakan otorisasi (Policy Engine).
 * Mendukung evaluasi method: viewAny, view, create, update, delete, approve.
 */
class PolicyResolver
{
    protected AuthorizationService $authService;

    public function __construct(?AuthorizationService $authService = null)
    {
        $this->authService = $authService ?? new AuthorizationService();
    }

    public function evaluate(AuthenticatedUser $user, string $ability, mixed $target = null): bool
    {
        // 1. Super Admin bypass check
        if ($this->authService->hasRole($user, 'SUPER_ADMIN')) {
            return true;
        }

        // 2. Policy Object Resolution if target is an object or policy class exists
        if (is_object($target)) {
            $policyClass = $this->resolvePolicyClass(get_class($target));
            if ($policyClass && class_exists($policyClass)) {
                $policy = new $policyClass();
                if (method_exists($policy, 'before')) {
                    $beforeResult = $policy->before($user, $ability);
                    if ($beforeResult !== null) {
                        return (bool) $beforeResult;
                    }
                }
                if (method_exists($policy, $ability)) {
                    return (bool) $policy->$ability($user, $target);
                }
            }
        }

        // 3. Fallback to Permission Code check (e.g. ability 'create' on 'jamaah' target -> 'jamaah.create')
        $module = is_string($target) ? strtolower($target) : (is_object($target) ? strtolower(basename(str_replace('\\', '/', get_class($target)))) : 'app');
        $permissionCode = sprintf('%s.%s', $module, strtolower($ability));

        return $this->authService->hasPermission($user, $permissionCode);
    }

    protected function resolvePolicyClass(string $entityClass): ?string
    {
        // E.g. App\Domains\Jamaah\Entities\Jamaah -> App\Domains\Jamaah\Policies\JamaahPolicy
        $parts = explode('\\', $entityClass);
        $entityName = array_pop($parts);

        if (count($parts) >= 2 && $parts[1] === 'Domains') {
            $domain = $parts[2];
            $policyClass = sprintf('App\\Domains\\%s\\Policies\\%sPolicy', $domain, $entityName);
            if (class_exists($policyClass)) {
                return $policyClass;
            }
        }

        return null;
    }
}
