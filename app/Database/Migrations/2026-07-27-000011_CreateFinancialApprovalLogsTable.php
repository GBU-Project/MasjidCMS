<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinancialApprovalLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'approver_user_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_id');
        $this->forge->addKey('approver_user_id');
        $this->forge->addKey('action');

        $this->forge->addForeignKey('transaction_id', 'financial_transactions', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('approval_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('approval_logs', true);
    }
}
