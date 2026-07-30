<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * UAT RC0-001 finding H: Agenda and Kajian were treated as one module.
 * Kajian (`kajian` table) is specifically for religious study sessions
 * (speaker_name, topic). Agenda is the general masjid activity/event
 * schedule (e.g. gotong royong, rapat DKM, peringatan hari besar) and needs
 * its own independent table, separate from Kajian's schema.
 */
class CreateAgendaTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('agenda')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'masjid_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'event_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'event_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'location' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'UPCOMING',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('event_date');
        $this->forge->createTable('agenda', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('agenda', true);
    }
}
