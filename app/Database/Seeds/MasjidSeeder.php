<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasjidSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'code'          => 'MSJ-YASMIN-001',
            'name'          => 'Masjid Raya Taman Yasmin',
            'slug'          => 'masjid-raya-taman-yasmin',
            'type'          => 'Masjid Raya',
            'email'         => 'info@masjidtamanyasmin.or.id',
            'phone'         => '0251-8312345',
            'website'       => 'https://masjidtamanyasmin.or.id',
            'address'       => 'Jl. KH. R. Abdullah Bin Nuh No. 1, Taman Yasmin',
            'district'      => 'Bogor Barat',
            'city'          => 'Kota Bogor',
            'province'      => 'Jawa Barat',
            'postal_code'   => '16113',
            'latitude'      => -6.56412300,
            'longitude'     => 106.77234500,
            'timezone'      => 'Asia/Jakarta',
            'logo_media_id' => null,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'created_by'    => 1,
        ];

        // Ensure clean insert or ignore on duplicate code
        $builder = $this->db->table('masjids');
        $existing = $builder->where('code', $data['code'])->get()->getRow();

        if (!$existing) {
            $builder->insert($data);
        }
    }
}
