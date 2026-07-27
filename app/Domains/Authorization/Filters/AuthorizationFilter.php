<?php

namespace App\Domains\Authorization\Filters;

use App\Core\Contracts\Auth\IdentityProviderInterface;
use App\Core\Contracts\Auth\PermissionProviderInterface;
use App\Domains\Authorization\Guards\GuardResolver;
use App\Domains\Authorization\Services\AuthorizationService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AuthorizationFilter
 *
 * CodeIgniter 4 HTTP Filter penangan evaluasi otorisasi route API.
 */
class AuthorizationFilter implements FilterInterface
{
    protected GuardResolver $guardResolver;
    protected PermissionProviderInterface $permissionProvider;

    public function __construct(
        ?GuardResolver $guardResolver = null,
        ?PermissionProviderInterface $permissionProvider = null
    ) {
        $this->guardResolver = $guardResolver ?? new GuardResolver();
        $this->permissionProvider = $permissionProvider ?? new AuthorizationService();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $user = $this->guardResolver->resolve($request);

        if (!$user) {
            $response = \Config\Services::response();
            return $response->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized: Authentication required',
            ])->setStatusCode(401);
        }

        // Check required permission if passed in filter arguments
        if (!empty($arguments)) {
            $requiredPermission = is_array($arguments) ? $arguments[0] : (string) $arguments;
            if (!$this->permissionProvider->hasPermission($user, $requiredPermission)) {
                $response = \Config\Services::response();
                return $response->setJSON([
                    'status'  => 'error',
                    'message' => sprintf('Forbidden: Missing permission [%s]', $requiredPermission),
                ])->setStatusCode(403);
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
