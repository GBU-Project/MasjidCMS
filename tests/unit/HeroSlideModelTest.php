<?php

namespace Tests\Unit;

use App\Models\HeroSlideModel;
use CodeIgniter\Test\CIUnitTestCase;

class HeroSlideModelTest extends CIUnitTestCase
{
    public function testHeroSlideModelAttributesAndUuidGeneration(): void
    {
        $uuid = HeroSlideModel::generateUuid();
        $this->assertNotEmpty($uuid);
        $this->assertSame(36, strlen($uuid));
    }

    public function testGetActiveSlidesFiltersPublishedAndNonExpired(): void
    {
        $model = new HeroSlideModel();
        
        // Ensure table exists (soft check)
        $db = \Config\Database::connect();
        if (!$db->tableExists('hero_slides')) {
            $this->markTestSkipped('Table hero_slides does not exist.');
        }

        $slides = $model->getActiveSlides();
        $this->assertIsArray($slides);

        foreach ($slides as $slide) {
            $this->assertSame('ACTIVE', $slide['status']);
            $this->assertNull($slide['deleted_at']);
            
            if ($slide['publish_at']) {
                $this->assertLessThanOrEqual(date('Y-m-d H:i:s'), $slide['publish_at']);
            }
            if ($slide['expire_at']) {
                $this->assertGreaterThanOrEqual(date('Y-m-d H:i:s'), $slide['expire_at']);
            }
        }
    }
}
