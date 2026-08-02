<?php

namespace Tests\Feature;

use App\Core\Security\SecurityContext;
use App\Domains\System\Entities\AuthenticatedUser;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class SecurityAuthorizationTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        SecurityContext::clear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        SecurityContext::clear();
    }

    /**
     * Test 1: Anonymous users are redirected to login when accessing protected admin financial export routes.
     */
    public function testAnonymousAccessToFinancialExportIsRedirectedToLogin(): void
    {
        $result = $this->get('admin/financial/export');
        $result->assertRedirectTo(site_url('login'));
    }

    /**
     * Test 2: Anonymous users are redirected to login when accessing financial mutation routes.
     */
    public function testAnonymousAccessToFinancialMutationIsRedirectedToLogin(): void
    {
        $result = $this->get('admin/financial/delete/1');
        $result->assertRedirectTo(site_url('login'));
    }

    /**
     * Test 3: Authenticated non-privileged user without financial.manage receives 403 Forbidden on financial export routes.
     */
    public function testNonPrivilegedUserIsDeniedAccessToFinancialExport(): void
    {
        $userData = [
            'id'          => 'u-operator',
            'username'    => 'operator',
            'displayName' => 'Takmir Operator',
            'email'       => 'operator@masjidcms.org',
            'roles'       => ['OPERATOR'],
            'permissions' => ['jamaah.read'],
        ];

        $result = $this->withSession(['auth_user' => $userData])
                       ->get('admin/financial/export');
        $result->assertStatus(403);
    }

    /**
     * Test 4: Authenticated non-privileged user is denied access to financial mutation routes.
     */
    public function testNonPrivilegedUserIsDeniedAccessToFinancialMutation(): void
    {
        $userData = [
            'id'          => 'u-operator',
            'username'    => 'operator',
            'displayName' => 'Takmir Operator',
            'email'       => 'operator@masjidcms.org',
            'roles'       => ['OPERATOR'],
            'permissions' => ['jamaah.read'],
        ];

        $result = $this->withSession(['auth_user' => $userData])
                       ->get('admin/financial/delete/1');
        $result->assertStatus(403);
    }

    /**
     * Test 5: Privileged Super Admin has bypass access to financial export routes.
     */
    public function testSuperAdminIsGrantedAccessToFinancialExport(): void
    {
        $userData = [
            'id'          => 'u-superadmin',
            'username'    => 'superadmin',
            'displayName' => 'Super Admin',
            'email'       => 'superadmin@masjidcms.org',
            'roles'       => ['SUPER_ADMIN'],
            'permissions' => [],
        ];

        $result = $this->withSession(['auth_user' => $userData])
                       ->get('admin/financial/export');
        $result->assertStatus(200);
    }

    /**
     * Test 6: Public portal routes are accessible anonymously without authentication.
     */
    public function testPublicPortalRoutesAccessibleAnonymously(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);

        $resultProfil = $this->get('profil');
        $resultProfil->assertStatus(200);
    }
}
