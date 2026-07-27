<?php

namespace Tests\Unit;

use App\Controllers\AdminDashboardController;
use PHPUnit\Framework\TestCase;

class DashboardDatabaseIntegrationTest extends TestCase
{
    public function testAdminDashboardControllerInstance(): void
    {
        $controller = new AdminDashboardController();
        $this->assertInstanceOf(AdminDashboardController::class, $controller);
    }
}
