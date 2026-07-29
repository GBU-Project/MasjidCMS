<?php

namespace Tests\Unit;

use App\Domains\System\Entities\AuthenticatedUser;
use PHPUnit\Framework\TestCase;

/**
 * Class SuperAdminBypassRegressionTest
 *
 * TASK-019A Audit Hotfix (29 Juli 2026).
 *
 * Sebelum patch ini, `AuthenticatedUser::isSuperAdmin()` hanya mencocokkan
 * literal role string 'superadmin'/'admin', padahal skema RBAC yang benar-benar
 * dipakai aplikasi (RbacSeeder / migration CreateRbacTables) memakai
 * `role_code` 'SUPER_ADMIN'. Digabung dengan bug lain (AuthenticationRepository
 * membaca kolom 'roles'/'permissions' yang tidak ada di tabel 'users'), akun
 * Super Admin manapun akan SELALU dianggap bukan Super Admin oleh
 * AuthorizationFilter, dan (karena RbacSeeder tidak pernah memberi
 * role_permissions eksplisit ke SUPER_ADMIN -- sengaja bergantung penuh pada
 * bypass ini) akan mendapat 403 di setiap rute `rbac:<permission>`.
 *
 * Test ini murni unit-level (tanpa DB) untuk mengunci perilaku entity secara
 * cepat di setiap environment, termasuk yang tidak punya database aktif.
 */
class SuperAdminBypassRegressionTest extends TestCase
{
    public function testUserWithSuperAdminRoleCodeIsRecognizedAsSuperAdmin(): void
    {
        $user = new AuthenticatedUser(
            id: 'u-01',
            username: 'superadmin',
            displayName: 'Super Administrator',
            email: 'admin@masjidcms.org',
            passwordHash: null,
            roles: ['SUPER_ADMIN'],
            permissions: []
        );

        $this->assertTrue(
            $user->isSuperAdmin(),
            'Akun dengan role_code SUPER_ADMIN (sesuai RbacSeeder) harus dikenali sebagai Super Admin.'
        );
    }

    public function testSuperAdminBypassesPermissionChecksEvenWithoutExplicitPermission(): void
    {
        $user = new AuthenticatedUser(
            id: 'u-01',
            username: 'superadmin',
            displayName: 'Super Administrator',
            email: 'admin@masjidcms.org',
            passwordHash: null,
            roles: ['SUPER_ADMIN'],
            permissions: [] // sengaja kosong -- bypass yang harus menutupinya
        );

        $this->assertTrue(
            $user->hasPermission('financial.manage'),
            'Super Admin harus tetap lolos pemeriksaan permission apa pun tanpa perlu grant eksplisit.'
        );
    }

    public function testRegularRoleIsNotTreatedAsSuperAdmin(): void
    {
        $user = new AuthenticatedUser(
            id: 'u-02',
            username: 'operator',
            displayName: 'Operator Takmir',
            email: 'operator@masjidcms.org',
            passwordHash: null,
            roles: ['OPERATOR'],
            permissions: ['jamaah.read']
        );

        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->hasPermission('financial.manage'));
        $this->assertTrue($user->hasPermission('jamaah.read'));
    }
}
