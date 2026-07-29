<?php

namespace Tests\Unit;

use App\Controllers\AdminMasterDataController;
use PHPUnit\Framework\TestCase;

class AdminCrudWorkspaceStandardizationTest extends TestCase
{
    public function testTableComponentDoesNotAutoGenerateActionColumn(): void
    {
        $content = file_get_contents(APPPATH . 'Views/components/table.php');

        // TASK-018: the shared table component must render ONLY the headers
        // and columns it is given, never a second hardcoded "Aksi" column.
        $this->assertStringNotContainsString('👁️ View', $content);
        $this->assertStringNotContainsString('📜 History', $content);
        $this->assertStringNotContainsString('<th style="text-align: right; padding-right: 24px;">Aksi</th>', $content);
    }

    public function testTableComponentBulkCheckboxIsOptInOnly(): void
    {
        $content = file_get_contents(APPPATH . 'Views/components/table.php');
        $this->assertStringContainsString('$enableBulk', $content);
    }

    public function testToolbarComponentHidesUnimplementedButtonsByDefault(): void
    {
        $content = file_get_contents(APPPATH . 'Views/components/toolbar.php');

        // Import/Export must default to hidden (null) unless a caller
        // explicitly provides a URL — no dummy buttons.
        $this->assertStringContainsString('$importUrl = $importUrl ?? null;', $content);
        $this->assertStringContainsString('$exportUrl = $exportUrl ?? null;', $content);
    }

    public function testMasterDataActionButtonsAreStandardizedAndFunctional(): void
    {
        $controller = new AdminMasterDataController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('actionButtons');
        $method->setAccessible(true);

        $html = $method->invoke($controller, '/admin/master/edit/jamaah/1', '/admin/master/delete/jamaahs/1', 'Hapus jamaah ini?');

        // Standard order: Edit before Delete, real hrefs (not "#"), and no
        // dummy View/History buttons since those actions aren't implemented.
        $editPos = strpos($html, '/admin/master/edit/jamaah/1');
        $deletePos = strpos($html, '/admin/master/delete/jamaahs/1');
        $this->assertNotFalse($editPos);
        $this->assertNotFalse($deletePos);
        $this->assertLessThan($deletePos, $editPos);
        $this->assertStringNotContainsString('👁️', $html);
        $this->assertStringNotContainsString('History', $html);
    }

    public function testMasterDataControllerHasNoDuplicateActionButtonMarkup(): void
    {
        $content = file_get_contents(APPPATH . 'Controllers/AdminMasterDataController.php');

        // The old inline "Edit"/"Hapus" text-button markup must be gone —
        // replaced by the single $this->actionButtons() helper.
        $this->assertStringNotContainsString('>Edit</a>', $content);
        $this->assertStringNotContainsString('>Hapus</a>', $content);
        $this->assertSame(8, substr_count($content, '$this->actionButtons('));
    }
}
