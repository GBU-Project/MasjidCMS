<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFamilyFieldsToJamaahsTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'family_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
                'after'      => 'marital_status',
            ],
            'family_relation_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'family_id',
            ],
        ];

        // Ensure columns do not already exist before adding
        if ($this->db->tableExists('jamaahs')) {
            if (!$this->db->fieldExists('family_id', 'jamaahs')) {
                $this->forge->addColumn('jamaahs', $fields);
            }
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('jamaahs')) {
            if ($this->db->fieldExists('family_id', 'jamaahs')) {
                $this->forge->dropColumn('jamaahs', ['family_id', 'family_relation_type']);
            }
        }
    }
}
