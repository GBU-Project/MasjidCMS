<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHomepageManagerSettings extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('settings')) {
            $defaultSettings = [
                'homepage_section_order' => json_encode([
                    'hero', 'prayer', 'profile', 'program', 'layanan', 'pengurus', 'kajian', 'gallery', 'donation'
                ]),
                'show_pengurus_section'  => '1',
                'show_program_section'   => '1',
                'show_layanan_section'   => '1',
                'show_bidang_section'    => '1',
                'show_kajian_section'    => '1',
                'show_gallery_section'   => '1',
                'show_profile_section'   => '1',
                'show_donation_section'  => '1',
                'donation_title'         => '💰 Mari Infaq & Sedekah Melalui {masjidName}',
                'donation_subtitle'      => 'Bantu operasional masjid & program sosial keumatan',
                'donation_description'   => 'Setiap rupiah donasi Anda disalurkan secara aman, akuntabel, dan terdaftar dalam Laporan Keuangan Transparan Masjid.',
                'donation_btn_text'      => 'Salurkan Donasi Sekarang ›',
                'donation_btn_url'       => 'donasi',
                'donation_bg_image'      => '',
                'limit_pengurus'         => '3',
                'limit_program'          => '6',
                'limit_kajian'           => '6',
                'limit_gallery'          => '8',
                'limit_layanan'          => '4',
                'limit_agenda'           => '5',
            ];

            foreach ($defaultSettings as $key => $val) {
                $check = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();
                if (!$check) {
                    $data = [
                        'setting_key'   => $key,
                        'setting_value' => $val,
                    ];
                    if ($this->db->fieldExists('setting_group', 'settings')) {
                        $data['setting_group'] = 'homepage';
                    }
                    $this->db->table('settings')->insert($data);
                }
            }
        }
    }

    public function down(): void
    {
        // Safe Rollback
    }
}
