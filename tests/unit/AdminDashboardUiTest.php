<?php

namespace Tests\Unit;

use App\Controllers\AdminDashboardController;
use PHPUnit\Framework\TestCase;

class AdminDashboardUiTest extends TestCase
{
    public function testAdminDashboardControllerInstance(): void
    {
        $controller = new AdminDashboardController();
        $this->assertInstanceOf(AdminDashboardController::class, $controller);
    }

    public function testDashboardViewFilesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/layouts/admin.php');
        $this->assertFileExists(APPPATH . 'Views/admin/dashboard/index.php');
        $this->assertFileExists(FCPATH . 'assets/css/admin-dashboard.css');
    }

    public function testDashboardViewContentContainsDesignSystemTokens(): void
    {
        $cssContent = file_get_contents(FCPATH . 'assets/css/admin-dashboard.css');
        $this->assertStringContainsString('--primary-500: #10B981', $cssContent);
        $this->assertStringContainsString('.stats-grid', $cssContent);
        $this->assertStringContainsString('.quick-action-bar', $cssContent);

        $viewContent = file_get_contents(APPPATH . 'Views/admin/dashboard/index.php');
        $this->assertStringContainsString('Dashboard Utama MasjidCMS', $viewContent);
        $this->assertStringContainsString('Saldo Kas Utama', $viewContent);
        $this->assertStringContainsString('Pending Approval', $viewContent);
    }
}
