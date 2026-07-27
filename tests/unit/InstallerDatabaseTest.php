<?php

namespace Tests\Unit;

use App\Services\Installer\DatabaseInstaller;
use PHPUnit\Framework\TestCase;

class InstallerDatabaseTest extends TestCase
{
    public function testDatabaseInstallerInstance(): void
    {
        $installer = new DatabaseInstaller();
        $this->assertInstanceOf(DatabaseInstaller::class, $installer);
    }
}
