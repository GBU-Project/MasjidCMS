<?php

namespace Tests\Unit;

use App\Domains\Authorization\Entities\Permission;
use App\Domains\Authorization\Entities\Role;
use App\Domains\Authorization\Entities\User;
use App\Domains\Authorization\Filters\AuthorizationFilter;
use App\Domains\Authorization\Guards\GuardResolver;
use App\Domains\Authorization\Services\AuthorizationService;
use App\Domains\Authorization\Services\PolicyResolver;
use App\Domains\System\Entities\AuthenticatedUser;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

class RbacFoundationRc1Test extends CIUnitTestCase
{
    public function testUserAndRoleEntities(): void
    {
        $user = User::fromArray([
            'id'       => 'usr-01',
            'username' => 'admin_test',
            'email'    => 'admin@masjid.org',
            'roles'    => ['ADMIN_MASJID'],
        ]);

        $this->assertSame('usr-01', $user->id);
        $this->assertTrue($user->isActive());
        $this->assertContains('ADMIN_MASJID', $user->roles);

        $role = Role::fromArray([
            'id'        => 'r-01',
            'role_code' => 'ADMIN_MASJID',
            'name'      => 'Admin Masjid',
        ]);
        $this->assertSame('ADMIN_MASJID', $role->role_code);

        $perm = Permission::fromArray([
            'id'              => 'p-01',
            'permission_code' => 'jamaah.create',
            'module_name'     => 'Jamaah',
        ]);
        $this->assertSame('jamaah.create', $perm->permission_code);
    }

    public function testRoleAssignmentAndPermissionCheck(): void
    {
        $authService = new AuthorizationService();

        $normalUser = new AuthenticatedUser(
            id: 'usr-normal',
            username: 'operator',
            roles: ['OPERATOR'],
            permissions: ['dashboard.view', 'jamaah.*', 'family.read']
        );

        $this->assertTrue($authService->hasRole($normalUser, 'OPERATOR'));
        $this->assertFalse($authService->hasRole($normalUser, 'SUPER_ADMIN'));

        // Direct & Wildcard Matching
        $this->assertTrue($authService->hasPermission($normalUser, 'dashboard.view'));
        $this->assertTrue($authService->hasPermission($normalUser, 'jamaah.create'));
        $this->assertTrue($authService->hasPermission($normalUser, 'jamaah.delete'));
        $this->assertTrue($authService->hasPermission($normalUser, 'family.read'));
        $this->assertFalse($authService->hasPermission($normalUser, 'admin.manage'));
    }

    public function testSuperAdminBypass(): void
    {
        $authService = new AuthorizationService();

        $superAdmin = new AuthenticatedUser(
            id: 'usr-super',
            username: 'superadmin',
            roles: ['SUPER_ADMIN'],
            permissions: []
        );

        $this->assertTrue($authService->hasRole($superAdmin, 'SUPER_ADMIN'));
        $this->assertTrue($authService->hasPermission($superAdmin, 'anything.at.all'));
        $this->assertTrue($authService->hasPermission($superAdmin, 'admin.manage'));
    }

    public function testPolicyEngineEvaluation(): void
    {
        $policyResolver = new PolicyResolver();

        $user = new AuthenticatedUser(
            id: 'usr-policy',
            username: 'sekretaris',
            roles: ['SEKRETARIS'],
            permissions: ['jamaah.create', 'jamaah.read', 'jamaah.update', 'jamaah.delete', 'jamaah.approve']
        );

        $this->assertTrue($policyResolver->evaluate($user, 'create', 'jamaah'));
        $this->assertTrue($policyResolver->evaluate($user, 'read', 'jamaah'));
        $this->assertTrue($policyResolver->evaluate($user, 'update', 'jamaah'));
        $this->assertTrue($policyResolver->evaluate($user, 'delete', 'jamaah'));
        $this->assertTrue($policyResolver->evaluate($user, 'approve', 'jamaah'));

        $this->assertFalse($policyResolver->evaluate($user, 'manage', 'system'));
    }

    public function testGuardResolution(): void
    {
        $guardResolver = new GuardResolver();

        // 1. Bearer Token Resolution
        $resolvedTokenUser = $guardResolver->resolveBearerToken('token-test-123');
        $this->assertNotNull($resolvedTokenUser);
        $this->assertSame('usr-token-01', $resolvedTokenUser->id);

        // Invalid Token
        $invalidTokenUser = $guardResolver->resolveBearerToken('invalid-token');
        $this->assertNull($invalidTokenUser);
    }

    public function testAuthorizationFilterInterception(): void
    {
        $mockGuard = $this->createMock(GuardResolver::class);
        $mockGuard->method('resolve')->willReturn(null); // Unauthenticated

        $filter = new AuthorizationFilter($mockGuard);

        $config = new \Config\App();
        $uri = new SiteURI($config);
        $request = new IncomingRequest($config, $uri, null, new UserAgent());

        $response = $filter->before($request);
        $this->assertNotNull($response);
        $this->assertSame(401, $response->getStatusCode());
    }

    public function testMultiTenantScopeAssignment(): void
    {
        $authService = new AuthorizationService();

        $assigned = $authService->assignMasjidRole('masjid-01', 'usr-01', 'r-admin-masjid-02');
        $this->assertTrue($assigned);
    }
}
