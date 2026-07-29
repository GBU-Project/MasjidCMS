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

    public function testSchemaAndSeedFilesAreConsistent(): void
    {
        $schemaSql = file_get_contents(ROOTPATH . 'database/schema.sql');
        $seedSql = file_get_contents(ROOTPATH . 'database/seed.sql');

        $this->assertNotEmpty($schemaSql);
        $this->assertNotEmpty($seedSql);
        $this->assertStringContainsString('ENGINE=InnoDB', $schemaSql);
    }
}
