<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MasjidProfileNarrativeFieldsTest extends TestCase
{
    public function testMigrationAddsExpectedColumns(): void
    {
        $path = APPPATH . 'Database/Migrations/2026-07-29-000016_AddProfileNarrativeFieldsToMasjidsTable.php';
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString("'history_text'", $content);
        $this->assertStringContainsString("'vision_text'", $content);
        $this->assertStringContainsString("'mission_text'", $content);
        // Must be idempotent / safe to run even if columns already exist.
        $this->assertStringContainsString('fieldExists', $content);
    }

    public function testAdminEditFormExposesNarrativeFields(): void
    {
        $content = file_get_contents(APPPATH . 'Views/admin/master/edit.php');
        $this->assertStringContainsString('name="history_text"', $content);
        $this->assertStringContainsString('name="vision_text"', $content);
        $this->assertStringContainsString('name="mission_text"', $content);
    }

    public function testMasterDataControllerUpdateWritesNarrativeFieldsDefensively(): void
    {
        $content = file_get_contents(APPPATH . 'Controllers/AdminMasterDataController.php');
        $this->assertStringContainsString('history_text', $content);
        $this->assertStringContainsString('vision_text', $content);
        $this->assertStringContainsString('mission_text', $content);
        // Update must check column existence before writing (safe on DBs
        // where the migration hasn't run yet).
        $this->assertMatchesRegularExpression(
            '/fieldExists\(\$narrativeField, \'masjids\'\)/',
            $content
        );
    }

    public function testPublicProfileViewRendersFromDatabaseNotHardcodedText(): void
    {
        $content = file_get_contents(APPPATH . 'Views/public/profile.php');

        // The old hardcoded placeholder copy must be gone.
        $this->assertStringNotContainsString('Masjid Agung Darussalam didirikan pada tahun 1995', $content);

        // Must read from $masjid and provide a graceful fallback when empty.
        $this->assertStringContainsString("\$masjid['history_text']", $content);
        $this->assertStringContainsString("\$masjid['vision_text']", $content);
        $this->assertStringContainsString("\$masjid['mission_text']", $content);
        $this->assertStringContainsString('belum diisi oleh pengurus', $content);
    }

    public function testMissionTextIsSplitLineByLineIntoBulletPoints(): void
    {
        $missionText = "Poin satu\nPoin dua\n\nPoin tiga";
        $points = array_values(array_filter(array_map('trim', explode("\n", $missionText))));

        $this->assertSame(['Poin satu', 'Poin dua', 'Poin tiga'], $points);
    }
}
