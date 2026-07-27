<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $familyId = 'f1a2m3i4-l5y6-7890-abcd-111122223333';
        $headId   = 'a1b2c3d4-e5f6-7890-abcd-111122223333'; // H. Ahmad Dahlan

        $familyData = [
            'id'             => $familyId,
            'family_no'      => 'KK-3271-2026-001',
            'kk_number'      => '3271011503850001',
            'name'           => 'Keluarga H. Ahmad Dahlan',
            'head_jamaah_id' => $headId,
            'address'        => 'Jl. Raya Yasmin No. 12',
            'district'       => 'Bogor Barat',
            'city'           => 'Kota Bogor',
            'province'       => 'Jawa Barat',
            'postal_code'    => '16113',
            'family_status'  => 'ACTIVE',
            'notes'          => 'Keluarga pengurus masjid',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        $familyBuilder = $this->db->table('families');
        $existing = $familyBuilder->where('family_no', $familyData['family_no'])->get()->getRow();

        if (!$existing) {
            $familyBuilder->insert($familyData);
        }

        // Link Jamaah members if jamaahs table has records
        $jamaahBuilder = $this->db->table('jamaahs');
        if ($this->db->tableExists('jamaahs')) {
            // Set Head
            $jamaahBuilder->where('id', $headId)->update([
                'family_id'            => $familyId,
                'family_relation_type' => 'HEAD',
            ]);

            // Set Wife if Hj. Siti Walidah exists
            $wifeId = 'b2c3d4e5-f6a7-8901-bcde-222233334444';
            $jamaahBuilder->where('id', $wifeId)->update([
                'family_id'            => $familyId,
                'family_relation_type' => 'WIFE',
            ]);
        }
    }
}
