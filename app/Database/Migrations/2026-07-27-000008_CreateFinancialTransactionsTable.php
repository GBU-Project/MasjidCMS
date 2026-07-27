<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinancialTransactionsTable extends Migration
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
            'fund_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'financial_account_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
            ],
            'program_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'jamaah_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'family_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'vendor_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'asset_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'transaction_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'transaction_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'CASH',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'DRAFT',
                'null'       => false,
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'approved_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'posted_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('transaction_no');
        $this->forge->addKey('masjid_id');
        $this->forge->addKey('fund_id');
        $this->forge->addKey('account_id');
        $this->forge->addKey('financial_account_id');
        $this->forge->addKey('program_id');
        $this->forge->addKey('jamaah_id');
        $this->forge->addKey('family_id');

        // Required Composite Indexes
        $this->forge->addKey(['transaction_date', 'fund_id'], false, false, 'idx_trx_date_fund');
        $this->forge->addKey(['status'], false, false, 'idx_trx_status');
        $this->forge->addKey(['jamaah_id', 'family_id'], false, false, 'idx_trx_cross_ref');

        $this->forge->addForeignKey('fund_id', 'funds', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('account_id', 'coa_accounts', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('financial_account_id', 'financial_accounts', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('financial_transactions', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('financial_transactions', true);
    }
}
