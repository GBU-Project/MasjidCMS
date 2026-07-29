<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\AuthorizationException;
use App\Core\Exceptions\ValidationException;
use App\Domains\System\DTO\LoginRequest;
use App\Domains\System\Services\AuthenticationService;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Class AuthPageController
 *
 * TASK-019A Security Blocker Remediation (29 Juli 2026).
 *
 * Menyediakan alur login berbasis browser (server-rendered form + session
 * cookie), melengkapi AuthenticationController (App\Domains\System) yang
 * sudah ada namun didesain sebagai endpoint JSON API murni untuk klien
 * programatik (SPA/mobile).
 *
 * Sebelum perbaikan ini, TIDAK ADA satupun rute login yang benar-benar
 * aktif di aplikasi (lihat app/Domains/System/Routes/auth.php yang tidak
 * pernah di-require), sementara seluruh rute admin/* sekarang mewajibkan
 * login (filter 'auth'). Controller ini mengisi celah tersebut.
 */
class AuthPageController extends BaseController
{
    protected AuthenticationService $authService;

    public function __construct(?AuthenticationService $authService = null)
    {
        $this->authService = $authService ?? new AuthenticationService();
    }

    /**
     * Menampilkan halaman form login. Jika pengguna sudah login,
     * langsung diarahkan ke dashboard admin.
     */
    public function showLogin(): string|RedirectResponse
    {
        if ($this->authService->check()) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    /**
     * Memproses submit form login (POST), memvalidasi kredensial via
     * AuthenticationService (bcrypt + SessionService yang sudah ada),
     * dan menerapkan rate-limiting yang sama seperti endpoint JSON API
     * (maks. 5 percobaan gagal / 5 menit per kombinasi IP+username).
     */
    public function login(): RedirectResponse
    {
        $username = (string) ($this->request->getPost('username') ?? '');
        $password = (string) ($this->request->getPost('password') ?? '');
        $remember = (bool) $this->request->getPost('remember');

        $ip = $this->request->getIPAddress();
        $throttleKey = 'login_page_attempts_' . md5($ip . '_' . $username);

        $throttler = \Config\Services::throttler();
        if ($throttler && !$throttler->check($throttleKey, 5, 300)) {
            return redirect()->to('/login')->withInput()
                ->with('error', 'Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam 5 menit.');
        }

        try {
            $loginDto = new LoginRequest(username: $username, password: $password, remember: $remember);
            $this->authService->login($loginDto);

            return redirect()->to('/admin/dashboard')->with('success', 'Login berhasil. Selamat datang kembali.');
        } catch (ValidationException|AuthorizationException $e) {
            return redirect()->to('/login')->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            log_message('error', 'Login page error: {msg}', ['msg' => $e->getMessage()]);
            return redirect()->to('/login')->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses login.');
        }
    }

    /**
     * Logout dan kembali ke halaman login.
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
