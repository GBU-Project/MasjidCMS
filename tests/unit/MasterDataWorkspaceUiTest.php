<?php

namespace Tests\Unit;

use App\Controllers\AdminCmsWorkspaceController;
use App\Controllers\AdminMasterDataController;
use PHPUnit\Framework\TestCase;

class MasterDataWorkspaceUiTest extends TestCase
{
    public function testAdminMasterDataControllerInstance(): void
    {
        $controller = new AdminMasterDataController();
        $this->assertInstanceOf(AdminMasterDataController::class, $controller);
    }

    public function testReusableComponentViewsExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/components/toolbar.php');
        $this->assertFileExists(APPPATH . 'Views/components/search_filter.php');
        $this->assertFileExists(APPPATH . 'Views/components/table.php');
        $this->assertFileExists(APPPATH . 'Views/components/pagination.php');
        $this->assertFileExists(APPPATH . 'Views/components/empty_state.php');
        $this->assertFileExists(APPPATH . 'Views/admin/master/index.php');
    }

    public function testMasterDataWorkspaceContainsReusableComponents(): void
    {
        $viewContent = file_get_contents(APPPATH . 'Views/admin/master/index.php');
        $this->assertStringContainsString("view('components/toolbar'", $viewContent);
        $this->assertStringContainsString("view('components/search_filter'", $viewContent);
        $this->assertStringContainsString("view('components/table'", $viewContent);
        $this->assertStringContainsString("view('components/pagination'", $viewContent);
    }

    public function testCmsEditMissingRecordReturnsRedirectResponse(): void
    {
        $controller = new AdminCmsWorkspaceController();
        $response = $controller->edit('posts', 'not-found-record');

        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
    }

    public function testMasterEditMissingRecordReturnsRedirectResponse(): void
    {
        $controller = new AdminMasterDataController();
        $response = $controller->edit('profil', 'not-found-record');

        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $response);
    }
}
