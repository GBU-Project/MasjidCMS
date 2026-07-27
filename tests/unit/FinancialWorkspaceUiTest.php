<?php

namespace Tests\Unit;

use App\Controllers\AdminFinancialWorkspaceController;
use PHPUnit\Framework\TestCase;

class FinancialWorkspaceUiTest extends TestCase
{
    public function testAdminFinancialWorkspaceControllerInstance(): void
    {
        $controller = new AdminFinancialWorkspaceController();
        $this->assertInstanceOf(AdminFinancialWorkspaceController::class, $controller);
    }

    public function testFinancialWorkspaceViewFilesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/admin/financial/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/financial/create.php');
        $this->assertFileExists(APPPATH . 'Views/admin/financial/detail.php');
    }

    public function testFinancialWorkspaceUsesReusableComponents(): void
    {
        $viewContent = file_get_contents(APPPATH . 'Views/admin/financial/index.php');
        $this->assertStringContainsString("view('components/toolbar'", $viewContent);
        $this->assertStringContainsString("view('components/search_filter'", $viewContent);
        $this->assertStringContainsString("view('components/table'", $viewContent);
        $this->assertStringContainsString("view('components/pagination'", $viewContent);
    }
}
