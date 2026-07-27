<?php

namespace Tests\Unit;

use App\Controllers\AdminReportingWorkspaceController;
use PHPUnit\Framework\TestCase;

class ReportingWorkspaceUiTest extends TestCase
{
    public function testAdminReportingWorkspaceControllerInstance(): void
    {
        $controller = new AdminReportingWorkspaceController();
        $this->assertInstanceOf(AdminReportingWorkspaceController::class, $controller);
    }

    public function testReportingWorkspaceViewFilesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/admin/reporting/index.php');
        $this->assertFileExists(APPPATH . 'Views/admin/reporting/preview.php');
    }

    public function testReportingWorkspaceUsesReusableComponents(): void
    {
        $previewContent = file_get_contents(APPPATH . 'Views/admin/reporting/preview.php');
        $this->assertStringContainsString("view('components/table'", $previewContent);
        $this->assertStringContainsString("view('components/pagination'", $previewContent);

        $indexContent = file_get_contents(APPPATH . 'Views/admin/reporting/index.php');
        $this->assertStringContainsString('Katalog Laporan Tersedia', $indexContent);
    }
}
