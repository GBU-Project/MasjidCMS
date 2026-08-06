<?php

namespace App\Domains\Financial\Controllers;

use App\Application\Financial\DTO\ApproveTransactionRequest;
use App\Application\Financial\DTO\CreateTransactionRequest;
use App\Application\Financial\DTO\PostTransactionRequest;
use App\Application\Financial\DTO\VoidTransactionRequest;
use App\Application\Financial\Services\ApproveTransactionApplicationService;
use App\Application\Financial\Services\CreateTransactionApplicationService;
use App\Application\Financial\Services\PostTransactionApplicationService;
use App\Application\Financial\Services\SubmitTransactionApplicationService;
use App\Application\Financial\Services\VoidTransactionApplicationService;
use App\Core\Controllers\BaseController;
use App\Core\Security\SecurityContext;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Exceptions\InvalidValueObjectException;
use App\Domains\Financial\Services\FinancialPostingService;
use App\Domains\Financial\Services\Posting\FinancialPostingEngine;
use App\Domains\Financial\Services\Posting\JournalBuilder;
use App\Domains\Financial\Services\Posting\LedgerPostingService;
use App\Domains\Financial\Services\Posting\PostingPolicy;
use App\Domains\Financial\Services\Posting\PostingValidator;
use App\Infrastructure\Persistence\Financial\Repositories\FinancialAccountRepository;
use App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository;
use App\Infrastructure\Persistence\Financial\Repositories\FundRepository;
use App\Infrastructure\Persistence\Financial\Repositories\JournalEntryRepository;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use Config\Database;
use Throwable;

class AdminFinancialWorkspaceController extends BaseController
{
    /**
     * @deprecated RC Blocker fix (see docs/Audit/RC_BLOCKER_RESOLUTION_REPORT.md):
     * FinancialPostingService::insertTransaction() bypassed the Financial
     * State Machine by writing transactions directly as POSTED via raw SQL.
     * That method call has been removed from this controller. This service
     * is kept only for generateUuid(), still used by non-transaction
     * master-data actions below (COA, budget, periods, journal templates).
     * Do NOT reintroduce calls to insertTransaction() here.
     */
    private FinancialPostingService $postingService;

    public function __construct()
    {
        $this->postingService = new FinancialPostingService();
    }

    /**
     * Current logged-in user id, taken from the server-side session
     * (SecurityContext), never trusted from client input. Follows the
     * same pattern already used in FinancialApiController::approve()
     * (TASK-019A hotfix) to prevent identity spoofing.
     */
    private function currentUserId(): string
    {
        return (string) (SecurityContext::user()->id ?? 'user-unknown');
    }

    private function newCreateService(): CreateTransactionApplicationService
    {
        return new CreateTransactionApplicationService(
            new FinancialTransactionRepository(),
            new FinancialUnitOfWork()
        );
    }

    private function newApproveService(): ApproveTransactionApplicationService
    {
        return new ApproveTransactionApplicationService(
            new FinancialTransactionRepository(),
            new FinancialUnitOfWork()
        );
    }

    private function newPostingEngine(FinancialUnitOfWork $uow): FinancialPostingEngine
    {
        return new FinancialPostingEngine(
            new PostingPolicy(),
            new PostingValidator(),
            new JournalBuilder(),
            new LedgerPostingService(),
            new FundRepository(),
            new FinancialAccountRepository(),
            new FinancialTransactionRepository(),
            new JournalEntryRepository(),
            $uow
        );
    }

    private function newPostService(): PostTransactionApplicationService
    {
        $uow = new FinancialUnitOfWork();

        return new PostTransactionApplicationService(
            new FinancialTransactionRepository(),
            $this->newPostingEngine($uow),
            $uow
        );
    }

    private function newVoidService(): VoidTransactionApplicationService
    {
        $uow = new FinancialUnitOfWork();

        return new VoidTransactionApplicationService(
            new FinancialTransactionRepository(),
            $this->newPostingEngine($uow),
            $uow
        );
    }

    public function index(): string
    {
        $db = Database::connect();
        $tab = (string) ($this->request->getGet('tab') ?? 'transactions');

        $headers = [];
        $rows = [];

        try {
            if ($tab === 'transactions' && $db->tableExists('financial_transactions')) {
                $headers = ['No. Transaksi', 'Tanggal', 'Jenis', 'Nominal (Rp)', 'Status', 'Aksi'];
                $data = $db->table('financial_transactions')->orderBy('created_at', 'DESC')->limit(100)->get()->getResultArray();
                foreach ($data as $t) {
                    $trxNo = $t['transaction_no'] ?? $t['transaction_number'] ?? ('TRX-' . $t['id']);
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono"><a href="' . site_url('admin/financial/detail/' . esc($trxNo)) . '">' . esc($trxNo) . '</a></span>',
                            esc(substr($t['transaction_date'], 0, 10)),
                            '<span class="badge ' . ($t['transaction_type'] === 'INCOME' ? 'badge-green' : 'badge-red') . '">' . esc($t['transaction_type']) . '</span>',
                            '<span class="stat-mono">' . number_format((float)$t['amount'], 0, ',', '.') . '</span>',
                            '<span class="badge ' . ($t['status'] === 'POSTED' ? 'badge-green' : 'badge-amber') . '">' . esc($t['status']) . '</span>',
                            '<div style="display:flex; gap:4px;">' .
                            '<a href="' . site_url('admin/financial/detail/' . $t['id']) . '" class="btn btn-secondary" style="padding: 2px 8px; font-size: 12px;">Detail</a>' .
                            $this->destructivePostButton(site_url('admin/financial/delete/' . $t['id']), 'Void', 'Void/Hapus transaksi ini?') .
                            '</div>',
                        ]
                    ];
                }
            } elseif ($tab === 'coa' && $db->tableExists('coa_accounts')) {
                $headers = ['Kode COA', 'Nama Akun', 'Tipe Akun', 'Status', 'Aksi'];
                $data = $db->table('coa_accounts')->get()->getResultArray();
                foreach ($data as $c) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($c['account_code']) . '</span>',
                            '<strong>' . esc($c['name']) . '</strong>',
                            '<span class="badge badge-green">' . esc($c['account_type']) . '</span>',
                            '<span class="badge ' . ($c['is_active'] ? 'badge-green' : 'badge-amber') . '">' . ($c['is_active'] ? 'ACTIVE' : 'INACTIVE') . '</span>',
                            $this->destructivePostButton(site_url('admin/financial/coa/delete/' . $c['id']), 'Hapus', 'Hapus COA ini?'),
                        ]
                    ];
                }
            } elseif ($tab === 'budget' && $db->tableExists('budget')) {
                $headers = ['Budget ID', 'Period ID', 'Allocated Amount', 'Used Amount', 'Aksi'];
                $data = $db->table('budget')->get()->getResultArray();
                foreach ($data as $b) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">BGT-' . esc($b['id']) . '</span>',
                            '<span class="stat-mono">PER-' . esc($b['period_id']) . '</span>',
                            '<span class="stat-mono">Rp ' . number_format((float)$b['allocated_amount'], 0, ',', '.') . '</span>',
                            '<span class="stat-mono">Rp ' . number_format((float)$b['used_amount'], 0, ',', '.') . '</span>',
                            $this->destructivePostButton(site_url('admin/financial/budget/delete/' . $b['id']), 'Hapus', 'Hapus anggaran ini?'),
                        ]
                    ];
                }
            } elseif ($tab === 'periods' && $db->tableExists('financial_periods')) {
                $headers = ['Kode Periode', 'Nama Periode', 'Tanggal Mulai', 'Tanggal Selesai', 'Status Closing', 'Aksi'];
                $data = $db->table('financial_periods')->get()->getResultArray();
                foreach ($data as $p) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($p['period_code']) . '</span>',
                            '<strong>' . esc($p['name']) . '</strong>',
                            esc($p['start_date']),
                            esc($p['end_date']),
                            '<span class="badge ' . ($p['is_closed'] ? 'badge-amber' : 'badge-green') . '">' . ($p['is_closed'] ? 'CLOSED' : 'OPEN') . '</span>',
                            $this->destructivePostButton(site_url('admin/financial/periods/delete/' . $p['id']), 'Hapus', 'Hapus periode ini?'),
                        ]
                    ];
                }
            } elseif ($tab === 'approvals' && $db->tableExists('approval_requests')) {
                $headers = ['Request ID', 'Transaction ID', 'Requester', 'Status', 'Tanggal Request'];
                $data = $db->table('approval_requests')->get()->getResultArray();
                foreach ($data as $a) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">REQ-' . esc($a['id']) . '</span>',
                            '<span class="stat-mono">TRX-' . esc($a['transaction_id']) . '</span>',
                            esc($a['requester_id']),
                            '<span class="badge badge-amber">' . esc($a['status']) . '</span>',
                            esc(substr($a['created_at'], 0, 16)),
                        ]
                    ];
                }
            } elseif ($tab === 'transfer' && $db->tableExists('funds')) {
                $headers = ['Kode Fund', 'Nama Kantong Dana', 'Tipe Syariah', 'Deskripsi'];
                $data = $db->table('funds')->get()->getResultArray();
                foreach ($data as $f) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($f['fund_code']) . '</span>',
                            '<strong>' . esc($f['name']) . '</strong>',
                            '<span class="badge badge-green">' . esc($f['fund_type']) . '</span>',
                            esc($f['description'] ?? '-'),
                        ]
                    ];
                }
            } elseif ($tab === 'journal' && $db->tableExists('journal_entries')) {
                $headers = ['No. Jurnal', 'Ref Transaksi', 'Tanggal', 'Keterangan', 'Status Balance'];
                $data = $db->table('journal_entries')->get()->getResultArray();
                foreach ($data as $j) {
                    $rows[] = [
                        'columns' => [
                            '<span class="stat-mono">' . esc($j['journal_number']) . '</span>',
                            '<span class="stat-mono">TRX-' . esc($j['transaction_id']) . '</span>',
                            esc(substr($j['journal_date'], 0, 10)),
                            esc($j['description']),
                            '<span class="badge badge-green">' . ($j['is_balanced'] ? 'BALANCED' : 'UNBALANCED') . '</span>',
                        ]
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Exception: ' . $e->getMessage());
        }

        $moduleLabels = [
            'transactions' => 'Daftar Transaksi',
            'coa'          => 'Chart of Accounts (COA)',
            'budget'       => 'Anggaran & RAB',
            'periods'      => 'Periode Akuntansi & Tutup Buku',
            'approvals'    => 'Queue Persetujuan DKM',
            'transfer'     => 'Transfer Kantong Dana',
            'journal'      => 'Buku Jurnal Umum',
        ];

        return view('admin/financial/index', [
            'activeTab'         => $tab,
            'activeModuleLabel' => $moduleLabels[$tab] ?? 'Daftar Transaksi',
            'headers'           => $headers,
            'rows'              => $rows,
        ]);
    }

    public function create(): string
    {
        $db = Database::connect();
        $funds = [];
        $financialAccounts = [];
        $coaAccounts = [];

        if ($db->tableExists('funds')) {
            $funds = $db->table('funds')->get()->getResultArray();
        }
        if ($db->tableExists('financial_accounts')) {
            $financialAccounts = $db->table('financial_accounts')->get()->getResultArray();
        }
        if ($db->tableExists('coa_accounts')) {
            $coaAccounts = $db->table('coa_accounts')->where('is_active', 1)->get()->getResultArray();
        }

        return view('admin/financial/create', [
            'funds'             => $funds,
            'financialAccounts' => $financialAccounts,
            'coaAccounts'       => $coaAccounts,
        ]);
    }

    public function store()
    {
        try {
            $rules = [
                'amount'               => 'required|numeric|greater_than[0]',
                'description'          => 'required',
                'fund_id'              => 'required|numeric',
                'financial_account_id' => 'required|numeric',
                'account_id'           => 'required|numeric',
            ];
            if (!$this->validate($rules)) {
                session()->setFlashdata('error', 'Gagal menyimpan transaksi: ' . implode(', ', $this->validator->getErrors()));
                return redirect()->back()->withInput();
            }

            // RC Blocker fix: transaksi baru sekarang WAJIB dibuat sebagai
            // DRAFT lewat Application Service + Entity yang sama dipakai
            // api/financial/transactions, bukan lagi INSERT mentah dengan
            // status POSTED. Lihat docs/Audit/RC_BLOCKER_RESOLUTION_REPORT.md.
            $req = new CreateTransactionRequest(
                '1', // masjid_id — instalasi single-tenant, konsisten dgn konvensi controller ini
                (int) $this->request->getPost('fund_id'),
                (int) $this->request->getPost('account_id'),
                (int) $this->request->getPost('financial_account_id'),
                $this->generateTransactionNo(),
                (string) ($this->request->getPost('transaction_type') ?: 'EXPENSE'),
                (float) $this->request->getPost('amount'),
                (string) ($this->request->getPost('transaction_date') ?: date('Y-m-d H:i:s')),
                null,
                null,
                null,
                null,
                null,
                'CASH',
                (string) $this->request->getPost('description'),
                $this->currentUserId()
            );

            $responseDto = $this->newCreateService()->execute($req);

            session()->setFlashdata(
                'success',
                'Transaksi ' . esc($responseDto->transactionNo) . ' disimpan sebagai DRAFT. ' .
                'Ajukan verifikasi dari halaman Detail sebelum bisa di-posting ke jurnal.'
            );
        } catch (InvalidValueObjectException|BusinessRuleException $e) {
            session()->setFlashdata('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
            return redirect()->back()->withInput();
        } catch (Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Store Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan transaksi: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }

        return redirect()->to(site_url('admin/financial'));
    }

    private function generateTransactionNo(): string
    {
        return 'TRX-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 9999), 5, '0', STR_PAD_LEFT);
    }

    public function export()
    {
        $db = Database::connect();
        $rows = [];
        if ($db->tableExists('financial_transactions')) {
            $rows = $db->table('financial_transactions')->orderBy('created_at', 'DESC')->get()->getResultArray();
        }

        $filename = 'financial-transactions-' . date('Ymd-His') . '.csv';
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['transaction_no', 'transaction_date', 'transaction_type', 'amount', 'status', 'fund_id', 'financial_account_id', 'account_id', 'description']);
        foreach ($rows as $r) {
            fputcsv($csv, [
                $r['transaction_no'] ?? '',
                $r['transaction_date'] ?? '',
                $r['transaction_type'] ?? '',
                $r['amount'] ?? 0,
                $r['status'] ?? '',
                $r['fund_id'] ?? '',
                $r['financial_account_id'] ?? '',
                $r['account_id'] ?? '',
                $r['description'] ?? '',
            ]);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function streamCsv(array $rows, array $headers, string $filenamePrefix)
    {
        $filename = $filenamePrefix . '-' . date('Ymd-His') . '.csv';
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, $headers);
        foreach ($rows as $r) {
            $line = [];
            foreach ($headers as $h) {
                $line[] = $r[$h] ?? '';
            }
            fputcsv($csv, $line);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    public function exportCoa()
    {
        $db = Database::connect();
        $rows = $db->tableExists('coa_accounts')
            ? $db->table('coa_accounts')->orderBy('account_code', 'ASC')->get()->getResultArray()
            : [];

        return $this->streamCsv($rows, ['account_code', 'name', 'account_type', 'is_active', 'created_at'], 'coa-accounts');
    }

    public function exportBudget()
    {
        $db = Database::connect();
        $rows = $db->tableExists('budget')
            ? $db->table('budget')->orderBy('period_id', 'ASC')->get()->getResultArray()
            : [];

        return $this->streamCsv($rows, ['period_id', 'account_id', 'fund_id', 'allocated_amount', 'used_amount'], 'budget-rab');
    }

    public function exportPeriods()
    {
        $db = Database::connect();
        $rows = $db->tableExists('financial_periods')
            ? $db->table('financial_periods')->orderBy('start_date', 'DESC')->get()->getResultArray()
            : [];

        return $this->streamCsv($rows, ['period_code', 'name', 'start_date', 'end_date', 'is_closed', 'created_at'], 'financial-periods');
    }

    public function exportJournal()
    {
        $db = Database::connect();
        $rows = $db->tableExists('journal_entries')
            ? $db->table('journal_entries')->orderBy('entry_date', 'DESC')->get()->getResultArray()
            : [];

        return $this->streamCsv($rows, ['journal_no', 'transaction_id', 'entry_date', 'description', 'created_at'], 'journal-entries');
    }

    public function import()
    {
        $file = $this->request->getFile('import_file');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File import tidak valid atau tidak ditemukan.');
            return redirect()->back();
        }

        $ext = strtolower($file->getClientExtension());
        if ($ext !== 'csv') {
            session()->setFlashdata('error', 'Format file harus CSV.');
            return redirect()->back();
        }

        $handle = fopen($file->getTempName(), 'r');
        if (!$handle) {
            session()->setFlashdata('error', 'File CSV tidak dapat dibaca.');
            return redirect()->back();
        }

        $header = fgetcsv($handle);
        $header = array_map('trim', $header ?: []);

        $imported = 0;
        $failed = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $assoc = array_combine($header, array_pad($row, count($header), null));
            if ($assoc === false) {
                $failed++;
                continue;
            }

            try {
                // RC Blocker fix: import kini memakai Application Service yang
                // sama dengan input manual (store()), sehingga baris hasil
                // import juga masuk sebagai DRAFT dan tetap wajib melalui
                // verifikasi + posting — bukan lagi INSERT langsung berstatus
                // POSTED. Ini memenuhi syarat "semua entry point konsisten".
                $req = new CreateTransactionRequest(
                    '1',
                    (int) ($assoc['fund_id'] ?? 0),
                    (int) ($assoc['account_id'] ?? 0),
                    (int) ($assoc['financial_account_id'] ?? 0),
                    $this->generateTransactionNo(),
                    (string) ($assoc['transaction_type'] ?? 'EXPENSE'),
                    (float) ($assoc['amount'] ?? 0),
                    (string) ($assoc['transaction_date'] ?? date('Y-m-d H:i:s')),
                    null,
                    null,
                    null,
                    null,
                    null,
                    'CASH',
                    (string) ($assoc['description'] ?? ''),
                    $this->currentUserId()
                );
                $this->newCreateService()->execute($req);
                $imported++;
            } catch (Throwable $e) {
                $failed++;
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }
        fclose($handle);

        $message = "Import selesai: {$imported} transaksi tersimpan sebagai DRAFT, {$failed} gagal. Transaksi hasil import tetap wajib diverifikasi & di-posting satu per satu.";
        if ($failed > 0) {
            session()->setFlashdata('error', $message . ' Detail: ' . implode(' | ', array_slice($errors, 0, 5)));
        } else {
            session()->setFlashdata('success', $message);
        }

        return redirect()->to(site_url('admin/financial'));
    }

    public function storeCoa()
    {
        $db = Database::connect();
        try {
            $code = (string) $this->request->getPost('account_code');
            $name = (string) $this->request->getPost('name');
            $type = (string) ($this->request->getPost('account_type') ?: 'ASSET');
            $uuid = $this->postingService->generateUuid();

            if (!empty($code) && !empty($name) && $db->tableExists('coa_accounts')) {
                $db->table('coa_accounts')->insert([
                    'uuid'         => $uuid,
                    'masjid_id'    => '1',
                    'account_code' => $code,
                    'name'         => $name,
                    'account_type' => $type,
                    'is_active'    => 1,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Akun COA ' . esc($code) . ' berhasil ditambahkan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController StoreCOA Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan COA: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=coa'));
    }

    public function deleteCoa(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('coa_accounts')) {
                $db->table('coa_accounts')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Akun COA berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController DeleteCOA Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus COA: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=coa'));
    }

    public function storeBudget()
    {
        $db = Database::connect();
        try {
            $allocated = (float) $this->request->getPost('allocated_amount');
            $periodId = (int) ($this->request->getPost('period_id') ?: 1);

            if ($allocated > 0 && $db->tableExists('budget')) {
                $db->table('budget')->insert([
                    'period_id'        => $periodId,
                    'account_id'       => 1,
                    'fund_id'          => 1,
                    'allocated_amount' => $allocated,
                    'used_amount'      => 0,
                ]);
                session()->setFlashdata('success', 'Alokasi Budget berhasil disimpan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController StoreBudget Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan Budget: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=budget'));
    }

    public function deleteBudget(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('budget')) {
                $db->table('budget')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Alokasi Budget berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController DeleteBudget Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus Budget: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=budget'));
    }

    public function storePeriod()
    {
        $db = Database::connect();
        try {
            $code = (string) $this->request->getPost('period_code');
            $name = (string) $this->request->getPost('name');
            $start = (string) $this->request->getPost('start_date');
            $end = (string) $this->request->getPost('end_date');

            if (!empty($code) && !empty($name) && $db->tableExists('financial_periods')) {
                $db->table('financial_periods')->insert([
                    'period_code' => $code,
                    'name'        => $name,
                    'start_date'  => $start ?: date('Y-01-01'),
                    'end_date'    => $end ?: date('Y-12-31'),
                    'is_closed'   => 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Periode Keuangan ' . esc($name) . ' berhasil ditambahkan.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController StorePeriod Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menyimpan Periode: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=periods'));
    }

    public function deletePeriod(string $id)
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('financial_periods')) {
                $db->table('financial_periods')->where('id', $id)->delete();
                session()->setFlashdata('success', 'Periode Keuangan berhasil dihapus.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController DeletePeriod Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal menghapus Periode: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=periods'));
    }

    public function storeJournal()
    {
        $db = Database::connect();
        try {
            $desc = (string) $this->request->getPost('description');
            $trxIdInput = (int) $this->request->getPost('transaction_id');

            if ($db->tableExists('journal_entries')) {
                $trxRow = null;
                if ($trxIdInput > 0) {
                    $trxRow = $db->table('financial_transactions')->where('id', $trxIdInput)->get()->getRowArray();
                } else {
                    $trxRow = $db->table('financial_transactions')->orderBy('id', 'DESC')->get()->getRowArray();
                }

                if (!$trxRow) {
                    session()->setFlashdata('error', 'Gagal memposting Jurnal: Belum ada transaksi keuangan yang dapat diajukan jurnalnya.');
                    return redirect()->to(site_url('admin/financial?tab=journal'));
                }

                $jNo = 'JRN-' . date('Ym') . '-' . str_pad((string) mt_rand(1, 9999), 5, '0', STR_PAD_LEFT);
                $uuid = $this->postingService->generateUuid();

                $db->table('journal_entries')->insert([
                    'uuid'           => $uuid,
                    'journal_no'     => $jNo,
                    'transaction_id' => $trxRow['id'],
                    'entry_date'     => date('Y-m-d H:i:s'),
                    'description'    => $desc ?: ('Jurnal Manual untuk ' . ($trxRow['transaction_no'] ?? ('TRX-' . $trxRow['id']))),
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                session()->setFlashdata('success', 'Catatan Jurnal Manual ' . esc($jNo) . ' berhasil diposting.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController StoreJournal Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal memposting Jurnal: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=journal'));
    }

    /**
     * RC Blocker fix: metode ini sebelumnya melakukan HARD DELETE baris
     * financial_transactions langsung dari database (ditemukan saat
     * penelusuran alur produksi untuk RC Blocker ini) — bertentangan
     * langsung dengan prinsip "VOID harus menghasilkan jurnal pembalik,
     * bukan menghapus data" di docs/FINANCIAL_GOVERNANCE_SPEC.md §5.
     * Sekarang memanggil VoidTransactionApplicationService, yang hanya
     * mengizinkan Void dari status POSTED dan membuat jurnal pembalik
     * lewat FinancialPostingEngine::voidPosting() — transaksi asal tetap
     * ada di riwayat dengan status VOID.
     */
    public function delete(string $id)
    {
        try {
            $trxRepo = new FinancialTransactionRepository();
            $transaction = is_numeric($id) ? $trxRepo->findById((int) $id) : $trxRepo->findByTransactionNo(new TransactionNumber($id));

            if (!$transaction) {
                session()->setFlashdata('error', 'Transaksi tidak ditemukan.');
                return redirect()->to(site_url('admin/financial?tab=transactions'));
            }

            $req = new VoidTransactionRequest($transaction->getUuid(), 'REV-' . $this->generateTransactionNo());
            $this->newVoidService()->execute($req);

            session()->setFlashdata('success', 'Transaksi berhasil di-VOID. Jurnal pembalik (reversal) telah dibuat.');
        } catch (EntityNotFoundException $e) {
            session()->setFlashdata('error', $e->getMessage());
        } catch (BusinessRuleException $e) {
            session()->setFlashdata('error', 'Gagal void transaksi: ' . $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController Void Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat void transaksi: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=transactions'));
    }

    /**
     * Admin UI action: DRAFT → PENDING_APPROVAL. Required before a
     * transaction can be approved and posted — closes the previous gap
     * where the Admin UI had no way to move a transaction through the
     * governed lifecycle at all.
     */
    public function submit(string $id)
    {
        return $this->transitionAction($id, function (string $uuid) {
            $service = new SubmitTransactionApplicationService(
                new FinancialTransactionRepository(),
                new FinancialUnitOfWork()
            );
            $service->execute($uuid);
        }, 'Transaksi diajukan untuk verifikasi (PENDING_APPROVAL).');
    }

    /**
     * Admin UI action: PENDING_APPROVAL → APPROVED. Enforces maker-checker
     * at the domain layer (FinancialTransaction::approve()) — a user
     * cannot approve a transaction they created themselves.
     */
    public function approveTransaction(string $id)
    {
        return $this->transitionAction($id, function (string $uuid) {
            $req = new ApproveTransactionRequest($uuid, $this->currentUserId());
            $this->newApproveService()->execute($req);
        }, 'Transaksi disetujui (APPROVED). Siap untuk posting ke jurnal.');
    }

    /**
     * Admin UI action: APPROVED → POSTED. As of the domain-layer fix in
     * FinancialTransaction::post(), this is the ONLY status the engine
     * will accept for posting — DRAFT is no longer allowed.
     */
    public function postTransaction(string $id)
    {
        return $this->transitionAction($id, function (string $uuid) {
            $req = new PostTransactionRequest($uuid, 'JRN-' . $this->generateTransactionNo(), 101);
            $this->newPostService()->execute($req);
        }, 'Transaksi berhasil di-posting ke jurnal double-entry. Status terkunci (immutable).');
    }

    /**
     * Shared helper: resolves a route id (numeric id or transaction_no)
     * to a UUID, runs the given state-transition callback, and handles
     * flashdata + redirect consistently for submit/approve/post.
     */
    private function transitionAction(string $id, callable $action, string $successMessage)
    {
        try {
            $trxRepo = new FinancialTransactionRepository();
            $transaction = is_numeric($id) ? $trxRepo->findById((int) $id) : $trxRepo->findByTransactionNo(new TransactionNumber($id));
            if (!$transaction) {
                session()->setFlashdata('error', 'Transaksi tidak ditemukan.');
                return redirect()->to(site_url('admin/financial?tab=transactions'));
            }

            $action($transaction->getUuid());
            session()->setFlashdata('success', $successMessage);
        } catch (EntityNotFoundException $e) {
            session()->setFlashdata('error', $e->getMessage());
        } catch (BusinessRuleException $e) {
            session()->setFlashdata('error', 'Aksi ditolak: ' . $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'AdminFinancialWorkspaceController transitionAction Exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/financial?tab=transactions'));
    }
}
