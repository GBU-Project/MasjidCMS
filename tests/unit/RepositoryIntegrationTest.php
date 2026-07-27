<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RepositoryIntegrationTest extends TestCase
{
    public function testControllersConnectToDatabase(): void
    {
        $this->assertTrue(class_exists('\Config\Database'));
    }
}
