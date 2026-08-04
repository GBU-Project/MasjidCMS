<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ForeignKeyTest extends TestCase
{
    public function testMigrationsContainForeignKeyConstraints(): void
    {
        $migrationContent = '';
        foreach (glob(APPPATH . 'Database/Migrations/*.php') as $migrationFile) {
            $migrationContent .= file_get_contents($migrationFile);
        }

        $this->assertStringContainsString("addForeignKey('fund_id', 'funds'", $migrationContent);
        $this->assertStringContainsString("addForeignKey('transaction_id', 'financial_transactions'", $migrationContent);
        $this->assertStringContainsString("addForeignKey('account_id', 'coa_accounts'", $migrationContent);
        $this->assertStringContainsString("addForeignKey('financial_account_id', 'financial_accounts'", $migrationContent);
    }
}
