<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinancialSeeder extends Seeder
{
    public function run(): void
    {
        $masjidId = 'm-01-default';

        // 1. Seed Default Funds
        $funds = [
            [
                'uuid'       => 'f-uuid-0001',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'GENERAL',
                'name'       => 'Kas Operasional Umum',
                'fund_type'  => 'UNRESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => 'f-uuid-0002',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'BUILDING',
                'name'       => 'Dana Pembangunan & Renovasi',
                'fund_type'  => 'RESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => 'f-uuid-0003',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'ZAKAT',
                'name'       => 'Dana ZISWAF (Zakat Fitrah/Mal)',
                'fund_type'  => 'RESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => 'f-uuid-0004',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'QURBAN',
                'name'       => 'Dana Operasional Qurban',
                'fund_type'  => 'RESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => 'f-uuid-0005',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'TPQ',
                'name'       => 'Dana Pendidikan TPQ',
                'fund_type'  => 'RESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => 'f-uuid-0006',
                'masjid_id'  => $masjidId,
                'fund_code'  => 'SOCIAL',
                'name'       => 'Dana Ambulans & Sosial',
                'fund_type'  => 'RESTRICTED',
                'status'     => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $fundsTable = $this->db->table('funds');
        foreach ($funds as $fund) {
            $existing = $fundsTable->where('fund_code', $fund['fund_code'])->get()->getRow();
            if (!$existing) {
                $fundsTable->insert($fund);
            }
        }

        // 2. Seed Default COA Accounts
        $coas = [
            [
                'uuid'         => 'coa-uuid-0001',
                'masjid_id'    => $masjidId,
                'account_code' => '10100',
                'name'         => 'Kas Tunai Utama',
                'account_type' => 'ASSET',
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'         => 'coa-uuid-0002',
                'masjid_id'    => $masjidId,
                'account_code' => '40100',
                'name'         => 'Infaq Kotak Jumat',
                'account_type' => 'INCOME',
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'         => 'coa-uuid-0003',
                'masjid_id'    => $masjidId,
                'account_code' => '50100',
                'name'         => 'Beban Listrik dan Air',
                'account_type' => 'EXPENSE',
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $coaTable = $this->db->table('coa_accounts');
        foreach ($coas as $coa) {
            $existing = $coaTable->where('account_code', $coa['account_code'])->get()->getRow();
            if (!$existing) {
                $coaTable->insert($coa);
            }
        }

        // 3. Seed Default Financial Account
        $finAccs = [
            [
                'uuid'           => 'fa-uuid-0001',
                'masjid_id'      => $masjidId,
                'code'           => 'KAS_UTAMA',
                'name'           => 'Kas Tunai Masjid Utama',
                'account_number' => '000-000-000',
                'bank_name'      => 'KAS TUNAI',
                'balance'        => 0.00,
                'created_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $finAccTable = $this->db->table('financial_accounts');
        foreach ($finAccs as $fa) {
            $existing = $finAccTable->where('code', $fa['code'])->get()->getRow();
            if (!$existing) {
                $finAccTable->insert($fa);
            }
        }
    }
}
