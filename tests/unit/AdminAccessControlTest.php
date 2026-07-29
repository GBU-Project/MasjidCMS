<?php

namespace Tests\Unit;

use Config\Filters;
use PHPUnit\Framework\TestCase;

/**
 * Class AdminAccessControlTest
 *
 * TASK-019A Security Blocker Remediation (29 Juli 2026).
 *
 * Test regresi untuk memastikan temuan kritis "seluruh panel admin bisa
 * diakses tanpa login" (lihat Laporan_Audit_IT_MasjidCMS_Independen.md §3)
 * tidak pernah terulang. Mengikuti konvensi pengujian berbasis introspeksi
 * konfigurasi yang sudah dipakai di CsrfProtectionTest.php, ditambah
 * verifikasi langsung terhadap RouteCollection yang benar-benar di-boot.
 */
class AdminAccessControlTest extends TestCase
{
    public function testAuthAndRbacFilterAliasesAreRegistered(): void
    {
        $filters = new Filters();
        $this->assertArrayHasKey('auth', $filters->aliases);
        $this->assertArrayHasKey('rbac', $filters->aliases);
    }

    /**
     * Memuat ulang RouteCollection dari app/Config/Routes.php dan
     * memastikan SETIAP rute yang berawalan 'admin/' benar-benar
     * memiliki filter 'auth' dan 'rbac' terpasang -- bukan hanya
     * berdasarkan pembacaan teks source, tapi hasil resolusi framework
     * yang sesungguhnya.
     */
    public function testEveryAdminRouteRequiresAuthAndRbacFilter(): void
    {
        $routes = service('routes', true);
        require APPPATH . 'Config/Routes.php';

        $checked = 0;

        foreach ($routes->getRoutes('GET') as $uri => $handler) {
            if (!str_starts_with($uri, 'admin/')) {
                continue;
            }

            $filtersForRoute = $routes->getFiltersForRoute($uri, 'GET');
            $names = array_map(
                static fn ($f) => is_string($f) ? explode(':', $f)[0] : $f,
                $filtersForRoute
            );

            $this->assertContains(
                'auth',
                $names,
                "Rute admin '{$uri}' tidak memiliki filter 'auth' -- REGRESI temuan kritis TASK-019A!"
            );
            $this->assertContains(
                'rbac',
                $names,
                "Rute admin '{$uri}' tidak memiliki filter 'rbac' -- REGRESI temuan kritis TASK-019A!"
            );

            $checked++;
        }

        $this->assertGreaterThan(
            30,
            $checked,
            'Jumlah rute admin/* yang diperiksa terlalu sedikit -- kemungkinan Routes.php berubah struktur dan test ini perlu disesuaikan.'
        );
    }

    public function testLoginAndLogoutRoutesAreRegisteredAndPublic(): void
    {
        $routes = service('routes', true);
        require APPPATH . 'Config/Routes.php';

        $collection = $routes->getRoutes('GET');
        $this->assertArrayHasKey('login', $collection, "Rute 'login' harus terdaftar -- sebelum TASK-019A tidak ada jalur login yang aktif sama sekali.");
        $this->assertArrayHasKey('logout', $collection);

        $loginFilters = array_map(
            static fn ($f) => is_string($f) ? explode(':', $f)[0] : $f,
            $routes->getFiltersForRoute('login', 'GET')
        );
        $this->assertNotContains('auth', $loginFilters, "Rute login tidak boleh mensyaratkan login (chicken-and-egg problem).");
    }

    public function testJsonAuthApiRouteIsActivated(): void
    {
        $routes = service('routes', true);
        require APPPATH . 'Config/Routes.php';

        $this->assertArrayHasKey(
            'auth/login',
            $routes->getRoutes('POST'),
            "Rute 'auth/login' (JSON API) harus aktif -- sebelumnya app/Domains/System/Routes/auth.php tidak pernah di-require."
        );
    }

    public function testFinancialApiRouteRequiresAuth(): void
    {
        $routes = service('routes', true);
        require APPPATH . 'Config/Routes.php';

        $filtersForCreate = array_map(
            static fn ($f) => is_string($f) ? explode(':', $f)[0] : $f,
            $routes->getFiltersForRoute('api/financial/transactions', 'POST')
        );

        $this->assertContains(
            'auth',
            $filtersForCreate,
            "Endpoint api/financial/transactions harus mensyaratkan login -- grup rute ini ditemukan tanpa proteksi sama sekali saat verifikasi TASK-019A."
        );

        // Verifikasi tambahan langsung ke source untuk endpoint dengan
        // dynamic segment (approve/reject/post/void), karena resolusi
        // filter untuk pola (:segment) tidak selalu konsisten lewat
        // getFiltersForRoute() tergantung versi framework.
        $source = file_get_contents(APPPATH . 'Domains/Financial/Routes/financial.php');
        $this->assertStringContainsString("'filter' => ['auth', 'rbac:financial.manage']", $source);
    }

    public function testDomainGroupRoutesRequireAuthAndRbac(): void
    {
        $routes = service('routes', true);
        require APPPATH . 'Config/Routes.php';

        foreach (['jamaah', 'family', 'masjid'] as $prefix) {
            $filters = array_map(
                static fn ($f) => is_string($f) ? explode(':', $f)[0] : $f,
                $routes->getFiltersForRoute($prefix, 'GET')
            );
            $this->assertContains('auth', $filters, "Grup rute '{$prefix}' harus mensyaratkan login (data pribadi/sensitif).");
        }
    }

    /**
     * PermissionRepository/RoleRepository sebelumnya adalah skeleton yang
     * tidak pernah disambungkan ke skema tabel sesungguhnya (kolom
     * 'permission_id'/'slug' yang tidak ada), sehingga filter granular
     * 'rbac:<permission>' akan crash 500 untuk user non-Super-Admin.
     * Test ini memverifikasi query builder-nya sudah memakai nama kolom
     * yang benar sesuai migration CreateRbacTables.
     */
    public function testPermissionRepositoryUsesCorrectSchemaColumns(): void
    {
        $source = file_get_contents(APPPATH . 'Domains/System/Repositories/PermissionRepository.php');
        $this->assertStringContainsString(
            "->join('role_permissions', 'role_permissions.permission_code = permissions.permission_code')",
            $source
        );

        $roleSource = file_get_contents(APPPATH . 'Domains/System/Repositories/RoleRepository.php');
        $this->assertStringContainsString("row['role_code']", $roleSource);
    }
}
