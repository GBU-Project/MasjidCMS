<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinancialJournalDetailsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'journal_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'debit_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'credit_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('journal_id');
        $this->forge->addKey('account_id');
        $this->forge->addKey(['journal_id', 'account_id'], false, false, 'idx_jrn_line_acc');

        $this->forge->addForeignKey('journal_id', 'journal_entries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('account_id', 'coa_accounts', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('journal_details', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('journal_details', true);
    }
}
