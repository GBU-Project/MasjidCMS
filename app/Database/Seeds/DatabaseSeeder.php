<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('MasjidSeeder');
        $this->call('RbacSeeder');
        $this->call('JamaahSeeder');
        $this->call('FamilySeeder');
        $this->call('FinancialSeeder');
        $this->call('MosqueBusinessSeeder');
    }
}
