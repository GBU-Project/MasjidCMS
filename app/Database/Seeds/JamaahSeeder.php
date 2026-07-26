<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JamaahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'code'       => 'Jamaah-001',
            'name'       => 'Default Sample Jamaah',
            'slug'       => 'default-sample-jamaah',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
        ];

        $builder = $this->db->table('jamaahs');
        $existing = $builder->where('code', $data['code'])->get()->getRow();

        if (!$existing) {
            $builder->insert($data);
        }
    }
}
