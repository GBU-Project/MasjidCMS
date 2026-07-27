<?php

namespace App\Services\Installer;

class AdminSeeder
{
    public function createAdmin(array $adminData): array
    {
        $password = $adminData['password'] ?? '';
        $confirmPassword = $adminData['confirm_password'] ?? '';

        if (empty($password) || strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password minimal 8 karakter.'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Konfirmasi password tidak cocok.'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $username = $adminData['username'] ?? 'superadmin';
        $email = $adminData['email'] ?? 'admin@masjidcms.org';
        $name = $adminData['name'] ?? 'Super Administrator';

        return [
            'success' => true,
            'user' => [
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'hash'     => $hash,
                'role'     => 'Super Admin',
            ],
        ];
    }
}
