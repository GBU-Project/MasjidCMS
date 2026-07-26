<?php

namespace App\Filters;

use App\Core\Exceptions\AuthorizationException;
use App\Core\Security\SecurityContext;
use App\Core\Support\ResponseFormatter;
use App\Domains\System\Services\RBACService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AuthorizationFilter
 *
 * Filter gerbang kedua dalam Security Pipeline.
 * Bertanggung jawab memverifikasi hak akses (RBAC) berdasarkan SecurityContext.
 * DILARANG membaca Session secara langsung (hanya membaca SecurityContext).
 */
class AuthorizationFilter implements FilterInterface
{
    protected RBACService $rbacService;

    public function __construct(?RBACService $rbacService = null)
    {
        $this->rbacService = $rbacService ?? new RBACService();
    }

    /**
     * Mengeksekusi verifikasi otorisasi sebelum request mencapai Controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = SecurityContext::user();

        if (!$user) {
            $response = service('response');
            return ResponseFormatter::error($response, 'Unauthenticated. Security context is empty.', null, 401);
        }

        // Super Admin Rule: bypass seluruh otorisasi jika user adalah Super Admin
        if ($user->isSuperAdmin()) {
            return null;
        }

        // Memeriksa permission parameter yang dikirimkan ke filter route
        if (!empty($arguments)) {
            $requiredPermission = is_array($arguments) ? $arguments[0] : (string) $arguments;

            try {
                $this->rbacService->authorize($user, $requiredPermission);
            } catch (AuthorizationException $e) {
                $response = service('response');
                return ResponseFormatter::error($response, $e->getMessage(), null, 403);
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
