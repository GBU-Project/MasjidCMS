<?php

namespace Tests\Unit;

use App\Services\Installer\InstallerLock;
use PHPUnit\Framework\TestCase;

class InstallerLockTest extends TestCase
{
    public function testLockCreationAndDetection(): void
    {
        $testFile = WRITEPATH . 'test_installed.lock';
        if (file_exists($testFile)) {
            unlink($testFile);
        }

        $lock = new InstallerLock($testFile);
        $this->assertFalse($lock->isInstalled());

        $success = $lock->createLock();
        $this->assertTrue($success);
        $this->assertTrue($lock->isInstalled());

        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }
}
