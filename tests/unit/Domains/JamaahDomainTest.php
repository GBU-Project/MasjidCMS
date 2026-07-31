<?php

namespace Tests\Unit\Domains;

use App\Core\Audit\AuditEntry;
use App\Core\Audit\Listeners\AuditEventListener;
use App\Core\Audit\Services\AuditService;
use App\Core\Contracts\Audit\AuditRepositoryInterface;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Events\EntityCreatedEvent;
use App\Core\Events\EntityDeletedEvent;
use App\Core\Events\EntityUpdatedEvent;
use App\Core\Events\EventDispatcher;
use App\Core\Exceptions\ValidationException;

use App\Domains\Jamaah\Controllers\JamaahController;
use App\Domains\Jamaah\DTO\CreateJamaahDTO;
use App\Domains\Jamaah\DTO\UpdateJamaahDTO;
use App\Domains\Jamaah\Entities\Jamaah;
use App\Domains\Jamaah\Repositories\JamaahRepository;
use App\Domains\Jamaah\Services\JamaahService;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

class JamaahDomainTest extends CIUnitTestCase
{
    public function testEntityInstantiation(): void
    {
        $data = [
            'id'        => '1',
            'member_no' => 'JM-TEST-01',
            'nik'       => '3201010101010001',
            'full_name' => 'Test Jamaah',
            'status'    => 'active',
        ];

        $entity = Jamaah::fromArray($data);
        $this->assertSame('1', $entity->id);
        $this->assertSame('JM-TEST-01', $entity->member_no);
        $this->assertSame('Test Jamaah', $entity->full_name);
        $this->assertTrue($entity->isActive());
    }

    public function testCreateAndUpdateDTO(): void
    {
        $createInput = [
            'member_no' => '  JM-DTO-01  ',
            'nik'       => '  3201010101010002  ',
            'full_name' => '  Sample Jamaah  ',
        ];

        $createDTO = CreateJamaahDTO::fromArray($createInput);
        $this->assertSame('JM-DTO-01', $createDTO->member_no);
        $this->assertSame('Sample Jamaah', $createDTO->full_name);
    }

    public function testServiceCrudLifecycle(): void
    {
        $store = [];

        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);

        $mockRepo->method('create')->willReturnCallback(function ($data) use (&$store) {
            $id = count($store) + 1;
            $data['id'] = $id;
            $store[$id] = $data;
            return $id;
        });

        $mockRepo->method('find')->willReturnCallback(function ($id) use (&$store) {
            return $store[$id] ?? null;
        });

        $mockRepo->method('update')->willReturnCallback(function ($id, $data) use (&$store) {
            if (!isset($store[$id])) return false;
            $store[$id] = array_merge($store[$id], $data);
            return true;
        });

        $mockRepo->method('delete')->willReturnCallback(function ($id) use (&$store) {
            if (!isset($store[$id])) return false;
            $store[$id]['deleted_at'] = date('Y-m-d H:i:s');
            return true;
        });

        $mockRepo->method('exists')->willReturnCallback(function ($id) use (&$store) {
            return isset($store[$id]);
        });

        $mockTxManager = $this->createMock(TransactionManagerInterface::class);
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);

        $service = new JamaahService($mockRepo, $mockDispatcher, $mockTxManager);

        $newId = $service->create([
            'member_no' => 'JM-CRUD-01',
            'nik'       => '3201010101010003',
            'full_name' => 'Jamaah CRUD Test',
        ]);
        $this->assertSame(1, $newId);

        $found = $service->find($newId);
        $this->assertNotNull($found);

        $updated = $service->update($newId, ['full_name' => 'Updated Jamaah']);
        $this->assertSame('Updated Jamaah', $updated['full_name']);

        $deleted = $service->delete($newId);
        $this->assertTrue($deleted);
    }

    public function testTransactionBoundaryAndRollback(): void
    {
        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willThrowException(new \RuntimeException('DB Error'));

        $txBegan = false;
        $txRolledBack = false;

        $mockTxManager = $this->createMock(TransactionManagerInterface::class);
        $mockTxManager->method('begin')->willReturnCallback(function() use (&$txBegan) {
            $txBegan = true;
        });
        $mockTxManager->method('rollback')->willReturnCallback(function() use (&$txRolledBack) {
            $txRolledBack = true;
        });

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);

        $service = new JamaahService($mockRepo, $mockDispatcher, $mockTxManager);

        try {
            $service->create([
                'member_no' => 'JM-FAIL-01',
                'nik'       => '3201010101010004',
                'full_name' => 'Fail Jamaah',
            ]);
            $this->fail('Expected Exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertTrue($txBegan);
            $this->assertTrue($txRolledBack);
        }
    }
}
