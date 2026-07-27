<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SeederIntegrityTest extends TestCase
{
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
