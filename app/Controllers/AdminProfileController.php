<?php

namespace App\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Security\SecurityContext;
use App\Domains\System\Services\AuthenticationService;
use Config\Database;

/**
 * Class AdminProfileController
 *
 * TASK-022 finding B: the admin header previously had no way for the
 * logged-in user to view their own account or change their password, and
 * no logout entry point from the avatar. This controller backs the new
 * "My Profile" / "Change Password" menu items added to the header dropdown
 * in app/Views/layouts/admin.php.
 */
class AdminProfileController extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $user = SecurityContext::user();

        return view('admin/profile/index', [
            'activePage' => 'profile',
            'user'       => $user,
        ]);
    }

    public function password(): string
    {
        return view('admin/profile/password', [
            'activePage' => 'profile',
            'user'       => SecurityContext::user(),
        ]);
    }

    public function updatePassword()
    {
        $user = SecurityContext::user();
        if (!$user) {
            return redirect()->to(site_url('login'));
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $authService = new AuthenticationService();

        if (!$authService->validateCredential($user->username, $currentPassword)) {
            session()->setFlashdata('error', 'Password saat ini tidak sesuai.');
            return redirect()->to(site_url('admin/profile/password'));
        }

        if (strlen($newPassword) < 8) {
            session()->setFlashdata('error', 'Password baru minimal 8 karakter.');
            return redirect()->to(site_url('admin/profile/password'));
        }

        if ($newPassword !== $confirmPassword) {
            session()->setFlashdata('error', 'Konfirmasi password baru tidak cocok.');
            return redirect()->to(site_url('admin/profile/password'));
        }

        $db = Database::connect();
        $db->table('users')
            ->where('id', $user->id)
            ->update([
                'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

        session()->setFlashdata('success', 'Password berhasil diperbarui.');
        return redirect()->to(site_url('admin/profile/password'));
    }
}
