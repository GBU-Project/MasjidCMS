<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * UAT RC0-001 findings E & G: there was no interface to manage a Favicon at
 * all (only `logo_media_id` existed), and Website Settings had no Social
 * Media fields. Both are additive, nullable columns — no existing data is
 * touched, and this is guarded to be safe on a database that already has
 * some of these columns (re-runnable / fresh-install safe, same defensive
 * pattern as AddProfileNarrativeFieldsToMasjidsTable).
 */
class AddFaviconAndSocialMediaFieldsToMasjidsTable extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('masjids')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('favicon_media_id', 'masjids')) {
            $fields['favicon_media_id'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ];
        }

        foreach (['facebook_url', 'instagram_url', 'youtube_url', 'whatsapp_number'] as $col) {
            if (!$this->db->fieldExists($col, 'masjids')) {
                $fields[$col] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ];
            }
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

        foreach (['favicon_media_id', 'facebook_url', 'instagram_url', 'youtube_url', 'whatsapp_number'] as $col) {
            if ($this->db->fieldExists($col, 'masjids')) {
                $this->forge->dropColumn('masjids', $col);
            }
        }
    }
}
