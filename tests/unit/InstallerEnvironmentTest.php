<?php

namespace Tests\Unit;

use App\Services\Installer\EnvironmentWriter;
use PHPUnit\Framework\TestCase;

class InstallerEnvironmentTest extends TestCase
{
    public function testGenerateAppKeyFormat(): void
    {
        $writer = new EnvironmentWriter();
        $key = $writer->generateAppKey();
        $this->assertStringStartsWith('hex2bin:', $key);
        $this->assertEquals(72, strlen($key)); // 'hex2bin:' + 64 hex chars
    }

    public function testWriteEnvironmentMock(): void
    {
        $writer = new EnvironmentWriter();
        $target = WRITEPATH . 'test_env.tmp';
        $success = $writer->writeEnvironment(['app_url' => 'http://localhost:8080/'], $target);
        $this->assertTrue($success);
        $this->assertFileExists($target);
        unlink($target);
    }
}
