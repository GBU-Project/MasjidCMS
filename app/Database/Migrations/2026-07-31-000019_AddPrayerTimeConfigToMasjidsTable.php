<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * TASK-022 finding G: masjids already had latitude/longitude/timezone, but
 * nothing to configure HOW the prayer schedule should be calculated
 * (calculation method, Asr madhab, high-latitude rule) -- the frontend
 * prayer widget was fully hardcoded instead. These three additive, nullable
 * columns (with sane ACS/Kemenag-style defaults) back the new Master Data ->
 * Profil Masjid -> Prayer Time configuration screen. Guarded/re-runnable
 * the same way as AddFaviconAndSocialMediaFieldsToMasjidsTable.
 */
class AddPrayerTimeConfigToMasjidsTable extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('masjids')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('prayer_calc_method', 'masjids')) {
            $fields['prayer_calc_method'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => 'KEMENAG',
            ];
        }

        if (!$this->db->fieldExists('prayer_asr_method', 'masjids')) {
            $fields['prayer_asr_method'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'STANDARD',
            ];
        }

        if (!$this->db->fieldExists('prayer_high_lat_rule', 'masjids')) {
            $fields['prayer_high_lat_rule'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'NONE',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('masjids', $fields);
        }
    }

    public function down(): void
    {
        if (!$this->db->tableExists('masjids')) {
            return;
        }

        foreach (['prayer_calc_method', 'prayer_asr_method', 'prayer_high_lat_rule'] as $col) {
            if ($this->db->fieldExists($col, 'masjids')) {
                $this->forge->dropColumn('masjids', $col);
            }
        }
    }
}
