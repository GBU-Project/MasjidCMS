<?php

namespace Tests\Unit;

use App\Application\Financial\DTO\FinancialTransactionResponse;
use App\Application\Financial\Services\ApproveTransactionApplicationService;
use App\Application\Financial\Services\CreateTransactionApplicationService;
use App\Application\Financial\Services\PostTransactionApplicationService;
use App\Application\Financial\Services\RejectTransactionApplicationService;
use App\Application\Financial\Services\TransferFundApplicationService;
use App\Application\Financial\Services\VoidTransactionApplicationService;
use App\Controllers\Api\FinancialApiController;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Exceptions\EntityNotFoundException;
use App\Domains\Financial\Exceptions\InvalidValueObjectException;
use App\Domains\Financial\Factories\FinancialTransactionFactory;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use PHPUnit\Framework\TestCase;

class FinancialApiControllerRc1Test extends TestCase
{
    private function createDummyResponseDto(): FinancialTransactionResponse
    {
        $trx = FinancialTransactionFactory::createDraft(
            'trx-uuid-999',
            'm-1',
            1,
            401,
            100,
            new TransactionNumber('TRX-202607-00999'),
            'INCOME',
            new Money(100000.00),
            '2026-07-27 10:00:00'
        );
        return FinancialTransactionResponse::fromEntity($trx);
    }

    public function testCreateTransactionEndpointSuccess(): void
    {
        $createService = $this->createMock(CreateTransactionApplicationService::class);
        $createService->method('execute')->willReturn($this->createDummyResponseDto());

        $controller = new FinancialApiController($createService);
        
        $request = $this->createMock(IncomingRequest::class);
        $request->method('getJSON')->willReturn([
            'masjid_id'            => 'm-1',
            'fund_id'              => 1,
            'account_id'           => 401,
            'financial_account_id' => 100,
            'transaction_no'       => 'TRX-202607-00999',
            'transaction_type'     => 'INCOME',
            'amount'               => 100000.00,
        ]);

        $response = new Response(new \Config\App());
        $controller->initController($request, $response, new \Psr\Log\NullLogger());

        $res = $controller->create();
        $this->assertSame(201, $res->getStatusCode());

        $body = json_decode($res->getBody(), true);
        $this->assertSame('success', $body['status']);
        $this->assertSame('TRX-202607-00999', $body['data']['transactionNo']);
    }

    public function testApproveTransactionEndpointSuccess(): void
    {
        $dto = $this->createDummyResponseDto();
        $dto->status = 'APPROVED';

        $approveService = $this->createMock(ApproveTransactionApplicationService::class);
        $approveService->method('execute')->willReturn($dto);

        $controller = new FinancialApiController(null, $approveService);

        $request = $this->createMock(IncomingRequest::class);
        $request->method('getJSON')->willReturn(['approver_user_id' => 'user-dkm-1']);

        $response = new Response(new \Config\App());
        $controller->initController($request, $response, new \Psr\Log\NullLogger());

        $res = $controller->approve('trx-uuid-999');
        $this->assertSame(200, $res->getStatusCode());

        $body = json_decode($res->getBody(), true);
        $this->assertSame('APPROVED', $body['data']['status']);
    }

    public function testErrorHandlingEntityNotFound(): void
    {
        $approveService = $this->createMock(ApproveTransactionApplicationService::class);
        $approveService->method('execute')->willThrowException(new EntityNotFoundException("Transaksi tidak ditemukan."));

        $controller = new FinancialApiController(null, $approveService);

        $request = $this->createMock(IncomingRequest::class);
        $request->method('getJSON')->willReturn(['approver_user_id' => 'user-dkm-1']);

        $response = new Response(new \Config\App());
        $controller->initController($request, $response, new \Psr\Log\NullLogger());

        $res = $controller->approve('non-existent-uuid');
        $this->assertSame(404, $res->getStatusCode());

        $body = json_decode($res->getBody(), true);
        $this->assertSame('error', $body['status']);
    }

    public function testErrorHandlingBusinessRuleViolation(): void
    {
        $transferService = $this->createMock(TransferFundApplicationService::class);
        $transferService->method('execute')->willThrowException(new BusinessRuleException("BR-FIN-01 Violation: Zakat restriction."));

        $controller = new FinancialApiController(null, null, null, null, null, $transferService);

        $request = $this->createMock(IncomingRequest::class);
        $request->method('getJSON')->willReturn([
            'source_fund_id' => 1,
            'target_fund_id' => 2,
            'amount'         => 50000.00,
        ]);

        $response = new Response(new \Config\App());
        $controller->initController($request, $response, new \Psr\Log\NullLogger());

        $res = $controller->transfer();
        $this->assertSame(409, $res->getStatusCode());

        $body = json_decode($res->getBody(), true);
        $this->assertStringContainsString('BR-FIN-01 Violation', $body['message']);
    }
}
