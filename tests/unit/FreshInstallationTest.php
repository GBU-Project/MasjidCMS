<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FreshInstallationTest extends TestCase
{
    public function testEnvironmentTemplateExists(): void
    {
        $this->assertFileExists(ROOTPATH . '.env.example');
        $envContent = file_get_contents(ROOTPATH . '.env.example');
        $this->assertStringContainsString('CI_ENVIRONMENT = production', $envContent);
        $this->assertStringContainsString('database.default.hostname', $envContent);
    }

    public function testFreshInstallUsesMigrationsAndSeeders(): void
    {
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        $seederFiles = glob(APPPATH . 'Database/Seeds/*.php');

        $this->assertNotEmpty($migrationFiles);
        $this->assertNotEmpty($seederFiles);

        $this->assertFileExists(APPPATH . 'Database/Migrations/2026-07-27-000003_CreateRbacTables.php');
        $this->assertFileExists(APPPATH . 'Database/Migrations/2026-07-27-000008_CreateFinancialTransactionsTable.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/RbacSeeder.php');
        $this->assertFileExists(APPPATH . 'Database/Seeds/FinancialSeeder.php');
    }

    public function testInstallationGuidesExist(): void
    {
        $this->assertTrue(
            file_exists(ROOTPATH . 'INSTALLATION.md') || file_exists(ROOTPATH . 'docs/archive/engine-docs/INSTALLATION_GUIDE.md')
        );
    }

    public function testInstalledLockIsIgnoredInGit(): void
    {
        $gitignore = file_get_contents(ROOTPATH . '.gitignore');
        $this->assertStringContainsString('/writable/installed.lock', $gitignore);
    }
}
