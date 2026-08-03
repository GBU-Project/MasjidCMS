<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('hero_slides') && $db->table('hero_slides')->countAllResults() === 0) {
            $defaultSlide = [
                'uuid'                => 'hero-default-0000-0000-000000000001',
                'title'               => 'Pusat Ibadah, Dakwah & Pemberdayaan Umat',
                'subtitle'            => 'Mewujudkan kemakmuran masjid melalui pelayanan jamaah yang transparan, modern, dan berkemajuan.',
                'bg_image_media_id'   => null,
                'primary_btn_text'    => 'Jelajahi Program DKM',
                'primary_btn_url'     => 'program',
                'primary_btn_new_tab' => 0,
                'secondary_btn_text'  => 'Infaq & Zakat Online',
                'secondary_btn_url'   => 'donasi',
                'secondary_btn_new_tab' => 0,
                'overlay_opacity'     => 40,
                'text_alignment'      => 'left',
                'status'              => 'ACTIVE',
                'sort_order'          => 1,
                'publish_at'          => null,
                'expire_at'           => null,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ];

            $db->table('hero_slides')->insert($defaultSlide);
        }
    }
}
