<?php

namespace App\Domains\Financial\Services;

use Config\Database;

class FinancialPostingService
{
    public function resolveDoubleEntryAccounts(string $type, int $financialAccountId, int $coaAccountId): array
    {
        if ($type === 'INCOME') {
            return [$financialAccountId, $coaAccountId];
        }

        return [$coaAccountId, $financialAccountId];
    }

    public function buildJournalDetailRows(string $type, float $amount, int $financialAccountId, int $coaAccountId): array
    {
        [$debitAccountId, $creditAccountId] = $this->resolveDoubleEntryAccounts($type, $financialAccountId, $coaAccountId);

        return [
            [
                'account_id'    => $debitAccountId,
                'debit_amount'  => $amount,
                'credit_amount' => 0.0,
            ],
            [
                'account_id'    => $creditAccountId,
                'debit_amount'  => 0.0,
                'credit_amount' => $amount,
            ],
        ];
    }

    /**
     * @deprecated RC Blocker fix (docs/Audit/RC_BLOCKER_RESOLUTION_REPORT.md):
     * this method wrote transactions directly to the database with
     * status POSTED, bypassing the Financial State Machine and the
     * Draft -> Pending Approval -> Approved -> Posted governance workflow
     * entirely. As of this fix, no caller in the codebase uses this method
     * anymore — AdminFinancialWorkspaceController::store()/import() now go
     * through CreateTransactionApplicationService instead, which creates
     * transactions as DRAFT via the Entity/Factory. Left in place
     * (unused) rather than deleted, to avoid removing code without being
     * able to verify no external/undiscovered caller depends on it in an
     * environment this audit could not fully inspect (e.g. custom forks).
     * Do not add new callers to this method.
     *
     * Inserts a single financial transaction + its auto double-entry journal.
     *
     * @return array{success: bool, message: string, transaction_no?: string}
     */
    public function insertTransaction(array $data): array
    {
        $db = Database::connect();

        $type      = (string) ($data['transaction_type'] ?: 'EXPENSE');
        $amount    = (float) ($data['amount'] ?? 0);
        $desc      = (string) ($data['description'] ?? '');
        $date      = (string) ($data['transaction_date'] ?: date('Y-m-d H:i:s'));
        $fundId    = (int) ($data['fund_id'] ?? 0);
        $finAccId  = (int) ($data['financial_account_id'] ?? 0);
        $accountId = (int) ($data['account_id'] ?? 0);

        if ($amount <= 0 || $desc === '' || $fundId <= 0 || $finAccId <= 0 || $accountId <= 0) {
            return ['success' => false, 'message' => 'Data tidak lengkap atau tidak valid.'];
        }

        $uuid = $this->generateUuid();
        $trxNo = 'TRX-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 9999), 5, '0', STR_PAD_LEFT);

        $db->transStart();

        $db->table('financial_transactions')->insert([
            'uuid'                 => $uuid,
            'masjid_id'            => '1',
            'fund_id'              => $fundId,
            'account_id'           => $accountId,
            'financial_account_id' => $finAccId,
            'transaction_no'       => $trxNo,
            'transaction_type'     => $type,
            'amount'               => $amount,
            'payment_method'       => 'CASH',
            'status'               => 'POSTED',
            'transaction_date'     => $date,
            'description'          => $desc,
            'created_at'           => date('Y-m-d H:i:s'),
        ]);
        $trxId = $db->insertID();

        if ($db->tableExists('financial_accounts')) {
            // Native CI4 pessimistic row locking (SELECT ... FOR UPDATE) inside transaction boundary
            $db->query('SELECT balance FROM financial_accounts WHERE id = ? FOR UPDATE', [$finAccId]);

            $builder = $db->table('financial_accounts')->where('id', $finAccId);
            if ($type === 'INCOME') {
                $builder->set('balance', 'balance + ' . $amount, false);
            } else {
                $builder->set('balance', 'balance - ' . $amount, false);
            }
            $builder->update();
        }

        if ($db->tableExists('journal_entries')) {
            $jUuid = $this->generateUuid();
            $jNo = 'JRN-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 9999), 5, '0', STR_PAD_LEFT);

            $db->table('journal_entries')->insert([
                'uuid'           => $jUuid,
                'transaction_id' => $trxId,
                'journal_no'     => $jNo,
                'entry_date'     => $date,
                'description'    => 'Jurnal Otomatis Transaksi ' . $trxNo . ': ' . $desc,
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $journalId = $db->insertID();

            if ($db->tableExists('journal_details')) {
                $journalDetailRows = $this->buildJournalDetailRows($type, $amount, $finAccId, $accountId);
                foreach ($journalDetailRows as $row) {
                    $db->table('journal_details')->insert([
                        'journal_id'    => $journalId,
                        'account_id'    => $row['account_id'],
                        'debit_amount'  => $row['debit_amount'],
                        'credit_amount' => $row['credit_amount'],
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Transaksi gagal diproses (rollback).'];
        }

        return ['success' => true, 'message' => 'OK', 'transaction_no' => $trxNo];
    }

    public function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
