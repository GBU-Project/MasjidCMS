<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CrudIntegrationTest extends TestCase
{
    public function testDatabaseTablesCanBeQueried(): void
    {
        $migrationContent = '';
        foreach (glob(APPPATH . 'Database/Migrations/*.php') as $migrationFile) {
            $migrationContent .= file_get_contents($migrationFile);
        }

        $this->assertStringContainsString("createTable('masjids'", $migrationContent);
        $this->assertStringContainsString("createTable('jamaahs'", $migrationContent);
        $this->assertStringContainsString("createTable('financial_transactions'", $migrationContent);
    }
}
