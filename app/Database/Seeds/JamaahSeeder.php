<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JamaahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id'             => 'a1b2c3d4-e5f6-7890-abcd-111122223333',
                'member_no'      => 'JM-2026-001',
                'nik'            => '3271011503850001',
                'full_name'      => 'H. Ahmad Dahlan',
                'gender'         => 'male',
                'birth_place'    => 'Bogor',
                'birth_date'     => '1985-03-15',
                'address'        => 'Jl. Raya Yasmin No. 12',
                'district'       => 'Bogor Barat',
                'city'           => 'Kota Bogor',
                'province'       => 'Jawa Barat',
                'postal_code'    => '16113',
                'phone'          => '081234567890',
                'email'          => 'ahmad.dahlan@jamaah.org',
                'occupation'     => 'Wiraswasta',
                'education'      => 'S1',
                'marital_status' => 'Married',
                'status'         => 'ACTIVE',
                'notes'          => 'Pengurus Dewan Pengkemas',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 'b2c3d4e5-f6a7-8901-bcde-222233334444',
                'member_no'      => 'JM-2026-002',
                'nik'            => '3271015208900002',
                'full_name'      => 'Hj. Siti Walidah',
                'gender'         => 'female',
                'birth_place'    => 'Bogor',
                'birth_date'     => '1990-08-22',
                'address'        => 'Jl. Raya Yasmin No. 15',
                'district'       => 'Bogor Barat',
                'city'           => 'Kota Bogor',
                'province'       => 'Jawa Barat',
                'postal_code'    => '16113',
                'phone'          => '081987654321',
                'email'          => 'siti.walidah@jamaah.org',
                'occupation'     => 'Guru',
                'education'      => 'S1',
                'marital_status' => 'Married',
                'status'         => 'ACTIVE',
                'notes'          => 'Kader Majelis Taklim Ibu-Ibu',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('jamaahs');
        foreach ($data as $item) {
            $existing = $builder->where('member_no', $item['member_no'])->get()->getRow();
            if (!$existing) {
                $builder->insert($item);
            }
        }
    }
}
