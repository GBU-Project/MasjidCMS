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

    public function testDatabaseSqlDumpFilesExist(): void
    {
        $this->assertFileExists(ROOTPATH . 'database/schema.sql');
        $this->assertFileExists(ROOTPATH . 'database/seed.sql');

        $schemaContent = file_get_contents(ROOTPATH . 'database/schema.sql');
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `funds`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `coa_accounts`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `financial_transactions`', $schemaContent);

        $seedContent = file_get_contents(ROOTPATH . 'database/seed.sql');
        $this->assertStringContainsString("INSERT INTO `funds`", $seedContent);
        $this->assertStringContainsString("superadmin", $seedContent);
    }

    public function testInstallationGuidesExist(): void
    {
        $this->assertFileExists(ROOTPATH . 'docs/INSTALLATION_GUIDE.md');
        $this->assertFileExists(ROOTPATH . 'docs/INSTALLATION_REPORT.md');
    }
}
