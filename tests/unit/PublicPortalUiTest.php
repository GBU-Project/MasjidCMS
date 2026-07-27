<?php

namespace Tests\Unit;

use App\Controllers\PublicPortalController;
use PHPUnit\Framework\TestCase;

class PublicPortalUiTest extends TestCase
{
    public function testPublicPortalControllerInstance(): void
    {
        $controller = new PublicPortalController();
        $this->assertInstanceOf(PublicPortalController::class, $controller);
    }

    public function testPublicPortalViewFilesExist(): void
    {
        $this->assertFileExists(APPPATH . 'Views/layouts/public.php');
        $this->assertFileExists(APPPATH . 'Views/public/index.php');
        $this->assertFileExists(APPPATH . 'Views/public/profile.php');
        $this->assertFileExists(APPPATH . 'Views/public/news.php');
        $this->assertFileExists(APPPATH . 'Views/public/programs.php');
        $this->assertFileExists(APPPATH . 'Views/public/donation.php');
        $this->assertFileExists(APPPATH . 'Views/public/contact.php');
        $this->assertFileExists(FCPATH . 'assets/css/public-portal.css');
    }

    public function testPublicPortalCssContent(): void
    {
        $cssContent = file_get_contents(FCPATH . 'assets/css/public-portal.css');
        $this->assertStringContainsString('--primary-500: #10B981', $cssContent);
        $this->assertStringContainsString('.hero-banner', $cssContent);
        $this->assertStringContainsString('.prayer-grid', $cssContent);
    }
}
