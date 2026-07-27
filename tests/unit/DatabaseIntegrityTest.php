<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    public function testSchemaFileExistsAndContains25Tables(): void
    {
        $schemaPath = ROOTPATH . 'database/schema.sql';
        $this->assertFileExists($schemaPath);
        $sql = file_get_contents($schemaPath);

        $tables = [
            'users', 'roles', 'permissions', 'role_permissions', 'user_roles',
            'masjids', 'branches', 'families', 'jamaah', 'jamaah_contacts', 'family_members',
            'funds', 'coa_accounts', 'financial_accounts', 'financial_periods', 'budget',
            'financial_transactions', 'journal_entries', 'journal_details',
            'approval_requests', 'approval_steps', 'approval_logs',
            'program_categories', 'programs', 'categories', 'pages', 'posts', 'menus', 'media', 'gallery',
            'settings', 'audit_logs', 'notifications'
        ];

        foreach ($tables as $table) {
            $this->assertStringContainsString("CREATE TABLE IF NOT EXISTS `{$table}`", $sql, "Table '{$table}' must exist in schema.sql");
        }
    }
}
