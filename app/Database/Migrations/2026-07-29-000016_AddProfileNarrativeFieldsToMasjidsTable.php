<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds narrative profile fields (Sejarah Singkat, Visi, Misi) to `masjids`
 * so the public "Profil & Visi Misi Masjid" page (/profil) can be managed
 * from the admin dashboard instead of showing hardcoded placeholder text.
 *
 * `mission_text` stores each mission point on its own line; the public view
 * splits on newline to render a bullet list.
 */
class AddProfileNarrativeFieldsToMasjidsTable extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('masjids')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('history_text', 'masjids')) {
            $fields['history_text'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('vision_text', 'masjids')) {
            $fields['vision_text'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('mission_text', 'masjids')) {
            $fields['mission_text'] = [
                'type' => 'TEXT',
                'null' => true,
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

        foreach (['history_text', 'vision_text', 'mission_text'] as $col) {
            if ($this->db->fieldExists($col, 'masjids')) {
                $this->forge->dropColumn('masjids', $col);
            }
        }
    }
}
