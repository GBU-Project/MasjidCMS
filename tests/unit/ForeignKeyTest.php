<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ForeignKeyTest extends TestCase
{
    public function testSchemaFileContainsForeignKeyConstraints(): void
    {
        $sql = file_get_contents(ROOTPATH . 'database/schema.sql');
        $this->assertStringContainsString('FOREIGN KEY (`fund_id`) REFERENCES `funds`', $sql);
        $this->assertStringContainsString('FOREIGN KEY (`transaction_id`) REFERENCES `financial_transactions`', $sql);
        $this->assertStringContainsString('FOREIGN KEY (`role_id`) REFERENCES `roles`', $sql);
        $this->assertStringContainsString('FOREIGN KEY (`user_id`) REFERENCES `users`', $sql);
    }
}
