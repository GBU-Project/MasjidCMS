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

    public function testRbacSeederCreatesDocumentedSuperAdminLogin(): void
    {
        $seederPath = APPPATH . 'Database/Seeds/RbacSeeder.php';
        $this->assertFileExists($seederPath);

        $content = file_get_contents($seederPath);

        $this->assertStringContainsString("'username'      => 'superadmin'", $content);
        $this->assertStringContainsString("'email'         => 'admin@masjidcms.org'", $content);
        $this->assertStringContainsString("password_hash('SuperAdminSecretPassword2026!'", $content);
        $this->assertStringContainsString("'user_id' => 'u-super-admin-01'", $content);
        $this->assertStringContainsString("'role_id' => 'r-super-admin-01'", $content);
    }

    public function testSeedFileExistsAndContainsInitialData(): void
    {
        $seedPath = ROOTPATH . 'database/seed.sql';
        $this->assertFileExists($seedPath);
        $sql = file_get_contents($seedPath);

        $this->assertStringContainsString("INSERT INTO `roles`", $sql);
        $this->assertStringContainsString("INSERT INTO `permissions`", $sql);
        $this->assertStringContainsString("INSERT INTO `users`", $sql);
        $this->assertStringContainsString("INSERT INTO `masjids`", $sql);
        $this->assertStringContainsString("INSERT INTO `funds`", $sql);
        $this->assertStringContainsString("INSERT INTO `coa_accounts`", $sql);
        $this->assertStringContainsString("INSERT INTO `settings`", $sql);
    }
}
