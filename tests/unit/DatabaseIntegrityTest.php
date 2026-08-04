<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    public function testMigrationsCoverCoreTables(): void
    {
        $migrationContent = '';
        foreach (glob(APPPATH . 'Database/Migrations/*.php') as $migrationFile) {
            $migrationContent .= file_get_contents($migrationFile);
        }

        $tables = [
            'users', 'roles', 'permissions', 'role_permissions', 'user_roles',
            'masjids', 'families', 'jamaahs', 'family_members',
            'funds', 'coa_accounts', 'financial_accounts', 'financial_periods', 'budget',
            'financial_transactions', 'journal_entries', 'journal_details',
            'approval_logs', 'program_categories', 'programs', 'categories', 'pages',
            'posts', 'menus', 'media', 'gallery', 'settings', 'audit_logs', 'notifications'
        ];

        foreach ($tables as $table) {
            $this->assertStringContainsString("createTable('{$table}'", $migrationContent, "Table '{$table}' must be created by migrations");
        }
    }
}
