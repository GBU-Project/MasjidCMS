<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinancialFundsTable extends Migration
{
    public function up(): void
    {
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
                'constraint' => 36,
                'null'       => false,
            ],
            'fund_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'fund_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'UNRESTRICTED',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'ACTIVE',
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('fund_code');
        $this->forge->addKey('masjid_id');
        $this->forge->addKey('fund_type');
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');

        $this->forge->createTable('funds', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('funds', true);
    }
}
