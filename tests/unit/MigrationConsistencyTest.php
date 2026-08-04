<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MigrationConsistencyTest extends TestCase
{
    public function testColumnAppendMigrationsDoNotDependOnBrittleAfterClauses(): void
    {
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');

        foreach ($migrationFiles as $migrationFile) {
            $content = file_get_contents($migrationFile);

            $this->assertStringNotContainsString(
                "'after'",
                $content,
                basename($migrationFile) . ' should not depend on physical column ordering'
            );
        }
    }

    public function testLegacySqlDumpFilesAreNonExecutableCompatibilityMarkers(): void
    {
        $schemaSql = file_get_contents(ROOTPATH . 'database/schema.sql');
        $seedSql = file_get_contents(ROOTPATH . 'database/seed.sql');

        $this->assertNotEmpty($schemaSql);
        $this->assertNotEmpty($seedSql);
        $this->assertStringContainsString('intentionally non-executable', $schemaSql);
        $this->assertStringContainsString('app/Database/Migrations/', $schemaSql);
        $this->assertStringNotContainsString('CREATE TABLE', $schemaSql);
        $this->assertStringContainsString('intentionally non-executable', $seedSql);
        $this->assertStringContainsString('app/Database/Seeds/', $seedSql);
        $this->assertStringNotContainsString('INSERT INTO', $seedSql);
    }
}
