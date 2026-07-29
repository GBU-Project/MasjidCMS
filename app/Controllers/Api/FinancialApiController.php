<?php

namespace App\Controllers\Api;

use App\Application\Financial\DTO\ApproveTransactionRequest;
use App\Application\Financial\DTO\CreateTransactionRequest;
use App\Application\Financial\DTO\PostTransactionRequest;
use App\Application\Financial\DTO\RejectTransactionRequest;
use App\Application\Financial\DTO\TransferFundRequest;
use App\Application\Financial\DTO\VoidTransactionRequest;
use App\Application\Financial\Services\ApproveTransactionApplicationService;
use App\Application\Financial\Services\CreateTransactionApplicationService;
use App\Application\Financial\Services\PostTransactionApplicationService;
use App\Application\Financial\Services\RejectTransactionApplicationService;
use App\Application\Financial\Services\TransferFundApplicationService;
use App\Application\Financial\Services\VoidTransactionApplicationService;
use App\Core\Controllers\BaseController;
use App\Core\Security\SecurityContext;
use App\Core\Support\ResponseFormatter;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Exceptions\InvalidValueObjectException;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class FinancialApiController extends BaseController
{
    private ?CreateTransactionApplicationService $createService = null;
    private ?ApproveTransactionApplicationService $approveService = null;
    private ?RejectTransactionApplicationService $rejectService = null;
    private ?PostTransactionApplicationService $postService = null;
    private ?VoidTransactionApplicationService $voidService = null;
    private ?TransferFundApplicationService $transferService = null;

    public function __construct(
        ?CreateTransactionApplicationService $createService = null,
        ?ApproveTransactionApplicationService $approveService = null,
        ?RejectTransactionApplicationService $rejectService = null,
        ?PostTransactionApplicationService $postService = null,
        ?VoidTransactionApplicationService $voidService = null,
        ?TransferFundApplicationService $transferService = null
    ) {
        $this->createService = $createService;
        $this->approveService = $approveService;
        $this->rejectService = $rejectService;
        $this->postService = $postService;
        $this->voidService = $voidService;
        $this->transferService = $transferService;
    }

    /**
     * POST /financial/transactions
     */
    public function create(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true) ?? [];
            
            $req = new CreateTransactionRequest(
                (string) ($json['masjid_id'] ?? 'm-01-default'),
                (int) ($json['fund_id'] ?? 0),
                (int) ($json['account_id'] ?? 0),
                (int) ($json['financial_account_id'] ?? 0),
                (string) ($json['transaction_no'] ?? ''),
                (string) ($json['transaction_type'] ?? ''),
                (float) ($json['amount'] ?? 0.0),
                (string) ($json['transaction_date'] ?? date('Y-m-d H:i:s')),
                isset($json['program_id']) ? (int) $json['program_id'] : null,
                $json['jamaah_id'] ?? null,
                $json['family_id'] ?? null,
                $json['vendor_id'] ?? null,
                $json['asset_id'] ?? null,
                (string) ($json['payment_method'] ?? 'CASH'),
                $json['description'] ?? null,
                $json['created_by'] ?? null
            );

            $service = $this->createService ?? new CreateTransactionApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork()
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Financial Transaction created successfully.', 201);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function approve(string $uuid): ResponseInterface
    {
        try {
            // TASK-019A Hotfix (patch audit, 29 Juli 2026): approver_user_id
            // SEBELUMNYA diambil dari body JSON milik client -- user yang
            // sudah login (siapa pun dengan permission 'financial.manage')
            // bisa memalsukan approver_user_id milik user lain. Diambil
            // paksa dari SecurityContext (sesi login), bukan input client.
            // Rute ini sudah mewajibkan filter 'auth' sehingga
            // SecurityContext::user() dijamin tidak null di titik ini.
            $approverUserId = (string) (SecurityContext::user()->id ?? 'user-dkm');
            $req = new ApproveTransactionRequest($uuid, $approverUserId);

            $service = $this->approveService ?? new ApproveTransactionApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork()
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Financial Transaction approved successfully.', 200);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function reject(string $uuid): ResponseInterface
    {
        try {
            // TASK-019A Hotfix (patch audit, 29 Juli 2026): lihat catatan
            // yang sama di approve() -- identitas penolak diambil dari
            // sesi login, bukan dari body JSON yang bisa dipalsukan.
            $json = $this->request->getJSON(true) ?? [];
            $req = new RejectTransactionRequest(
                $uuid,
                (string) (SecurityContext::user()->id ?? 'user-dkm'),
                $json['notes'] ?? null
            );

            $service = $this->rejectService ?? new RejectTransactionApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork()
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Financial Transaction rejected.', 200);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function post(string $uuid): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true) ?? [];
            $req = new PostTransactionRequest(
                $uuid,
                (string) ($json['journal_no'] ?? ''),
                (int) ($json['cash_account_id'] ?? 101)
            );

            $uow = new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork();
            $service = $this->postService ?? new PostTransactionApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Domains\Financial\Services\Posting\FinancialPostingEngine(
                    new \App\Domains\Financial\Services\Posting\PostingPolicy(),
                    new \App\Domains\Financial\Services\Posting\PostingValidator(),
                    new \App\Domains\Financial\Services\Posting\JournalBuilder(),
                    new \App\Domains\Financial\Services\Posting\LedgerPostingService(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FundRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FinancialAccountRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\JournalEntryRepository(),
                    $uow
                ),
                $uow
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Financial Transaction posted to double-entry ledger.', 200);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function void(string $uuid): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true) ?? [];
            $req = new VoidTransactionRequest(
                $uuid,
                (string) ($json['reversal_journal_no'] ?? '')
            );

            $uow = new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork();
            $service = $this->voidService ?? new VoidTransactionApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Domains\Financial\Services\Posting\FinancialPostingEngine(
                    new \App\Domains\Financial\Services\Posting\PostingPolicy(),
                    new \App\Domains\Financial\Services\Posting\PostingValidator(),
                    new \App\Domains\Financial\Services\Posting\JournalBuilder(),
                    new \App\Domains\Financial\Services\Posting\LedgerPostingService(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FundRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FinancialAccountRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                    new \App\Infrastructure\Persistence\Financial\Repositories\JournalEntryRepository(),
                    $uow
                ),
                $uow
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Financial Transaction voided with reversal journal.', 200);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function transfer(): ResponseInterface
    {
        try {
            $json = $this->request->getJSON(true) ?? [];
            $req = new TransferFundRequest(
                (string) ($json['masjid_id'] ?? 'm-01-default'),
                (int) ($json['source_fund_id'] ?? 0),
                (int) ($json['target_fund_id'] ?? 0),
                (int) ($json['source_financial_account_id'] ?? 0),
                (int) ($json['target_financial_account_id'] ?? 0),
                (int) ($json['transfer_account_id'] ?? 0),
                (string) ($json['transaction_no'] ?? ''),
                (float) ($json['amount'] ?? 0.0),
                (string) ($json['transaction_date'] ?? date('Y-m-d H:i:s')),
                $json['description'] ?? null,
                $json['created_by'] ?? null
            );

            $service = $this->transferService ?? new TransferFundApplicationService(
                new \App\Infrastructure\Persistence\Financial\Repositories\FundRepository(),
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Domains\Financial\Services\FinancialDomainService(),
                new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork()
            );

            $responseDto = $service->execute($req);

            return ResponseFormatter::success($this->response, $responseDto, 'Fund transfer transaction recorded.', 201);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    private function handleException(Throwable $e): ResponseInterface
    {
        if ($e instanceof EntityNotFoundException) {
            return ResponseFormatter::error($this->response, $e->getMessage(), null, 404);
        }

        if ($e instanceof InvalidValueObjectException) {
            return ResponseFormatter::error($this->response, $e->getMessage(), null, 400);
        }

        if ($e instanceof BusinessRuleException) {
            return ResponseFormatter::error($this->response, $e->getMessage(), null, 409);
        }

        return ResponseFormatter::error($this->response, 'An unexpected financial error occurred: ' . $e->getMessage(), null, 500);
    }

    private function resolveService(string $class): object
    {
        try {
            return new $class(
                new \App\Infrastructure\Persistence\Financial\Repositories\FinancialTransactionRepository(),
                new \App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork()
            );
        } catch (Throwable $e) {
            return new \stdClass();
        }
    }
}
