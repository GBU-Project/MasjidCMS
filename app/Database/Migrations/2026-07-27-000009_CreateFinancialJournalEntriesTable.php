<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinancialJournalEntriesTable extends Migration
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
            'transaction_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'journal_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'entry_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('journal_no');
        $this->forge->addUniqueKey('transaction_id');
        $this->forge->addKey(['entry_date'], false, false, 'idx_jrn_date');

        $this->forge->addForeignKey('transaction_id', 'financial_transactions', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('journal_entries', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('journal_entries', true);
    }
}
