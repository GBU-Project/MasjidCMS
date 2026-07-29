<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFamilyFieldsToJamaahsTable extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('jamaahs')) {
            return;
        }

        if (!$this->db->fieldExists('family_id', 'jamaahs')) {
            $this->forge->addColumn('jamaahs', [
                'family_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                    'null'       => true,
                ],
            ]);
        }

        if (!$this->db->fieldExists('family_relation_type', 'jamaahs')) {
            $this->forge->addColumn('jamaahs', [
                'family_relation_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (!$this->db->tableExists('jamaahs')) {
            return;
        }

        foreach (['family_relation_type', 'family_id'] as $column) {
            if ($this->db->fieldExists($column, 'jamaahs')) {
                $this->forge->dropColumn('jamaahs', $column);
            }
        }
    }
}
