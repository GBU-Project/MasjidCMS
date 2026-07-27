<?php

namespace Tests\Unit;

use App\Services\Installer\RequirementChecker;
use PHPUnit\Framework\TestCase;

class InstallerRequirementTest extends TestCase
{
    public function testRequirementCheckerPhpVersion(): void
    {
        $checker = new RequirementChecker();
        $php = $checker->checkPhpVersion();
        $this->assertArrayHasKey('pass', $php);
        $this->assertTrue($php['pass']);
    }

    public function testRequirementCheckerExtensionsAndPermissions(): void
    {
        $checker = new RequirementChecker();
        $exts = $checker->checkExtensions();
        $this->assertNotEmpty($exts);

        $perms = $checker->checkPermissions();
        $this->assertNotEmpty($perms);
    }
}
