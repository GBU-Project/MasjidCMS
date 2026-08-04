<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SeederIntegrityTest extends TestCase
{
    public function testMasjidSeederChecksAllUniqueNaturalKeys(): void
    {
        $seederPath = APPPATH . 'Database/Seeds/MasjidSeeder.php';
        $this->assertFileExists($seederPath);

        $content = file_get_contents($seederPath);

        $this->assertStringContainsString("->where('code'", $content);
        $this->assertStringContainsString("->orWhere('slug'", $content);
        $this->assertStringContainsString("->orWhere('email'", $content);
    }

    /**
     * TASK-022 (finding A): RbacSeeder used to auto-create a hardcoded
     * 'superadmin' / 'admin@masjidcms.org' user with a hardcoded password.
     * That account was created on every fresh install before the installer
     * wizard ran, so a user who picked "superadmin" as their own username
     * collided with it and could not complete installation — and it shipped
     * a publicly-known default credential as a standing security risk.
     *
     * RbacSeeder must now seed roles/permissions only. The installer wizard
     * (AdminSeeder::createAdmin + InstallerController::persistAdminUser) is
     * the sole source of the initial admin account.
     */
    public function testRbacSeederDoesNotCreateHardcodedSuperAdminLogin(): void
    {
        $seederPath = APPPATH . 'Database/Seeds/RbacSeeder.php';
        $this->assertFileExists($seederPath);

        $content = file_get_contents($seederPath);

        $this->assertStringNotContainsString("'username'      => 'superadmin'", $content);
        $this->assertStringNotContainsString("'email'         => 'admin@masjidcms.org'", $content);
        $this->assertStringNotContainsString('SuperAdminSecretPassword2026!', $content);
        $this->assertStringNotContainsString("'user_id' => 'u-super-admin-01'", $content);

        // Roles/permissions seeding must remain intact.
        $this->assertStringContainsString("'role_code'   => 'SUPER_ADMIN'", $content);
    }

    public function testInitialDataComesFromCodeIgniterSeeders(): void
    {
        $seeders = [
            APPPATH . 'Database/Seeds/RbacSeeder.php',
            APPPATH . 'Database/Seeds/MasjidSeeder.php',
            APPPATH . 'Database/Seeds/FinancialSeeder.php',
        ];

        foreach ($seeders as $seederPath) {
            $this->assertFileExists($seederPath);
        }

        $this->assertStringContainsString("'role_code'   => 'SUPER_ADMIN'", file_get_contents($seeders[0]));
        $this->assertStringContainsString("'code'          => 'MSJ-YASMIN-001'", file_get_contents($seeders[1]));
        $this->assertStringContainsString("'fund_code'  => 'GENERAL'", file_get_contents($seeders[2]));
    }
}
