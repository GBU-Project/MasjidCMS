<?php

namespace Tests\Unit;

use App\Controllers\PublicPortalController;
use PHPUnit\Framework\TestCase;

class HomepageDatabaseIntegrationTest extends TestCase
{
    public function testPublicPortalControllerInstance(): void
    {
        $controller = new PublicPortalController();
        $this->assertInstanceOf(PublicPortalController::class, $controller);
    }
}
