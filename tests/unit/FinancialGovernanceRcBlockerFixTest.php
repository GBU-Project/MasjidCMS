<?php

namespace Tests\Unit;

use App\Application\Financial\Services\ApproveTransactionApplicationService;
use App\Application\Financial\Services\CreateTransactionApplicationService;
use App\Application\Financial\Services\SubmitTransactionApplicationService;
use App\Domains\Financial\Entities\FinancialTransaction;
use App\Domains\Financial\Entities\ValueObjects\Money;
use App\Domains\Financial\Entities\ValueObjects\TransactionNumber;
use App\Domains\Financial\Exceptions\BusinessRuleException;
use App\Domains\Financial\Repositories\Contracts\FinancialTransactionRepositoryInterface;
use App\Infrastructure\Persistence\Financial\UnitOfWork\FinancialUnitOfWork;
use PHPUnit\Framework\TestCase;

class FinancialGovernanceRcBlockerFixTest extends TestCase
{
    private function createDummyTransaction(string $createdBy = 'user-maker', string $status = 'DRAFT'): FinancialTransaction
    {
        return new FinancialTransaction(
            1,
            'trx-uuid-12345',
            '1',
            1,
            101,
            201,
            new TransactionNumber('TRX-202608-00001'),
            'EXPENSE',
            new Money(150000.0),
            '2026-08-06 10:00:00',
            null,
            null,
            null,
            null,
            null,
            'CASH',
            $status,
            'Pengeluaran Operasional Kebersihan',
            $createdBy
        );
    }

    public function testCreateTransactionAlwaysStartsAsDraft(): void
    {
        $repoMock = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $uowMock = $this->createMock(FinancialUnitOfWork::class);

        $repoMock->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (FinancialTransaction $trx) {
                $this->assertSame('DRAFT', $trx->getStatus());
                return $trx;
            });

        $service = new CreateTransactionApplicationService($repoMock, $uowMock);
        $req = new \App\Application\Financial\DTO\CreateTransactionRequest(
            '1', 1, 101, 201, 'TRX-202608-00001', 'EXPENSE', 150000.0, '2026-08-06 10:00:00',
            null, null, null, null, null, 'CASH', 'Test', 'user-maker'
        );

        $res = $service->execute($req);
        $this->assertSame('DRAFT', $res->status);
    }

    public function testPostFromDraftIsRejected(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'DRAFT');

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Transaksi harus melalui status APPROVED terlebih dahulu');

        $trx->post('2026-08-06 12:00:00');
    }

    public function testPostFromApprovedSucceeds(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'APPROVED');
        $trx->post('2026-08-06 12:00:00');

        $this->assertSame('POSTED', $trx->getStatus());
        $this->assertSame('2026-08-06 12:00:00', $trx->getPostedAt());
    }

    public function testMakerCannotApproveOwnTransaction(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'PENDING_APPROVAL');

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('Transaksi tidak dapat disetujui oleh pembuatnya sendiri (maker-checker)');

        $trx->approve('user-maker');
    }

    public function testDifferentUserCanApprove(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'PENDING_APPROVAL');
        $trx->approve('user-checker');

        $this->assertSame('APPROVED', $trx->getStatus());
        $this->assertSame('user-checker', $trx->getApprovedBy());
    }

    public function testApproveTransactionApplicationServiceEnforcesMakerChecker(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'PENDING_APPROVAL');

        $repoMock = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $uowMock = $this->createMock(FinancialUnitOfWork::class);

        $repoMock->method('findByUuid')->willReturn($trx);

        $service = new ApproveTransactionApplicationService($repoMock, $uowMock);
        $req = new \App\Application\Financial\DTO\ApproveTransactionRequest('trx-uuid-12345', 'user-maker');

        $this->expectException(BusinessRuleException::class);
        $this->expectExceptionMessage('maker-checker');

        $service->execute($req);
    }

    public function testSubmitTransactionApplicationServiceMovesToPendingApproval(): void
    {
        $trx = $this->createDummyTransaction('user-maker', 'DRAFT');

        $repoMock = $this->createMock(FinancialTransactionRepositoryInterface::class);
        $uowMock = $this->createMock(FinancialUnitOfWork::class);

        $repoMock->method('findByUuid')->willReturn($trx);
        $repoMock->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (FinancialTransaction $saved) {
                $this->assertSame('PENDING_APPROVAL', $saved->getStatus());
                return $saved;
            });

        $service = new SubmitTransactionApplicationService($repoMock, $uowMock);
        $res = $service->execute('trx-uuid-12345');

        $this->assertSame('PENDING_APPROVAL', $res->status);
    }
}
