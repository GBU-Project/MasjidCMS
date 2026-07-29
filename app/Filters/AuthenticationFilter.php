<?php

namespace App\Filters;

use App\Core\Security\SecurityContext;
use App\Core\Support\ResponseFormatter;
use App\Domains\System\Services\AuthenticationService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AuthenticationFilter
 *
 * Filter gerbang pertama dalam Security Pipeline.
 * Bertanggung jawab memverifikasi otentikasi sesi dan mengisi SecurityContext.
 * DILARANG melakukan pengecekan otorisasi (RBAC).
 */
class AuthenticationFilter implements FilterInterface
{
    protected AuthenticationService $authService;

    public function __construct(?AuthenticationService $authService = null)
    {
        $this->authService = $authService ?? new AuthenticationService();
    }

    /**
     * Mengeksekusi verifikasi otentikasi sebelum request mencapai Controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = $this->authService->currentUser();

        if (!$user) {
            SecurityContext::clear();

            // TASK-019A Security Blocker Remediation (29 Juli 2026):
            // Sebelumnya filter ini selalu mengembalikan JSON 401 mentah,
            // termasuk untuk navigasi browser biasa ke /admin/*. Sekarang
            // request non-API/non-AJAX diarahkan (redirect) ke halaman
            // login yang baru diaktifkan, sementara klien API/JSON tetap
            // menerima 401 terstruktur seperti sebelumnya.
            helper('url');
            $uri = ltrim($request->getUri()->getPath(), '/');
            $isApiRequest = str_starts_with($uri, 'api/') || $request->isAJAX();

            if ($isApiRequest) {
                $response = service('response');
                return ResponseFormatter::error($response, 'Unauthenticated. Please log in first.', null, 401);
            }

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        // Set user terotentikasi ke SecurityContext
        SecurityContext::setUser($user);

        return null;
    }

    /**
     * Callback setelah Controller selesai dieksekusi.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Cleaning SecurityContext
        SecurityContext::clear();
        return null;
    }
}
