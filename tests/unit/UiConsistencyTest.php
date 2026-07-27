<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UiConsistencyTest extends TestCase
{
    public function testFormComponentsExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/components/form/input.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/textarea.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/select.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/date.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/currency.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/checkbox.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/switch.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/validation.php');
        $this->assertFileExists(APPPATH . 'Views/components/form/form_group.php');
    }

    public function testLoadingAndFeedbackComponentsExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/components/loading.php');
        $this->assertFileExists(APPPATH . 'Views/components/modal.php');
        $this->assertFileExists(APPPATH . 'Views/components/alert.php');
    }

    public function testErrorPagesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/errors/html/error_403.php');
        $this->assertFileExists(APPPATH . 'Views/errors/html/error_404.php');
        $this->assertFileExists(APPPATH . 'Views/errors/html/error_500.php');
    }

    public function testCssVariablesEngineFileExists(): void
    {
        $this->assertFileExists(FCPATH . 'assets/css/app-theme.css');
        $cssContent = file_get_contents(FCPATH . 'assets/css/app-theme.css');
        $this->assertStringContainsString('--color-primary', $cssContent);
        $this->assertStringContainsString('--color-success', $cssContent);
        $this->assertStringContainsString('--color-danger', $cssContent);
    }
}
