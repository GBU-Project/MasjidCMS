<?php

namespace Tests\Unit;

use Config\Filters;
use Config\Security;
use PHPUnit\Framework\TestCase;

class CsrfProtectionTest extends TestCase
{
    public function testCsrfFilterIsEnabledGlobally(): void
    {
        $filtersConfig = new Filters();
        $this->assertArrayHasKey('csrf', $filtersConfig->aliases);
        $this->assertArrayHasKey('csrf', $filtersConfig->globals['before']);
    }

    public function testSecurityConfigTokenRandomization(): void
    {
        $securityConfig = new Security();
        $this->assertTrue($securityConfig->tokenRandomize);
        $this->assertSame('csrf_masjidcms_token', $securityConfig->tokenName);
    }
}
