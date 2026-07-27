<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CrudIntegrationTest extends TestCase
{
    public function testDatabaseTablesCanBeQueried(): void
    {
        $schemaSql = file_get_contents(ROOTPATH . 'database/schema.sql');
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `masjids`', $schemaSql);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `jamaah`', $schemaSql);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `financial_transactions`', $schemaSql);
    }
}
