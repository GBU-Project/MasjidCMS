<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHomepageVisibilityAndOrderingFields extends Migration
{
    public function up(): void
    {
        // 1. Table bidang
        if ($this->db->tableExists('bidang')) {
            if (!$this->db->fieldExists('homepage_visible', 'bidang')) {
                $this->forge->addColumn('bidang', [
                    'homepage_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                ]);
            }
            if (!$this->db->fieldExists('featured', 'bidang')) {
                $this->forge->addColumn('bidang', [
                    'featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                ]);
            }
        }

        // 2. Table pengurus
        if ($this->db->tableExists('pengurus')) {
            if (!$this->db->fieldExists('homepage_visible', 'pengurus')) {
                $this->forge->addColumn('pengurus', [
                    'homepage_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                ]);
            }
            if (!$this->db->fieldExists('featured', 'pengurus')) {
                $this->forge->addColumn('pengurus', [
                    'featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                ]);
            }
        }

        // 3. Table program_kegiatan
        if ($this->db->tableExists('program_kegiatan')) {
            if (!$this->db->fieldExists('homepage_visible', 'program_kegiatan')) {
                $this->forge->addColumn('program_kegiatan', [
                    'homepage_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                ]);
            }
            if (!$this->db->fieldExists('sort_order', 'program_kegiatan')) {
                $this->forge->addColumn('program_kegiatan', [
                    'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                ]);
            }
        }

        // 4. Table layanan_masjid
        if ($this->db->tableExists('layanan_masjid')) {
            if (!$this->db->fieldExists('homepage_visible', 'layanan_masjid')) {
                $this->forge->addColumn('layanan_masjid', [
                    'homepage_visible' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                ]);
            }
            if (!$this->db->fieldExists('featured', 'layanan_masjid')) {
                $this->forge->addColumn('layanan_masjid', [
                    'featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                ]);
            }
        }
    }

    public function down(): void
    {
        // Safe Rollback
    }
}
