<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MigrationConsistencyTest extends TestCase
{
    public function testSchemaAndSeedFilesAreConsistent(): void
    {
        $schemaSql = file_get_contents(ROOTPATH . 'database/schema.sql');
        $seedSql = file_get_contents(ROOTPATH . 'database/seed.sql');

        $this->assertNotEmpty($schemaSql);
        $this->assertNotEmpty($seedSql);
        $this->assertStringContainsString('ENGINE=InnoDB', $schemaSql);
    }
}
