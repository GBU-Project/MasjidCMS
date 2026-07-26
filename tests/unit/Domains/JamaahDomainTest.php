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
            'id'     => 1,
            'code'   => 'Jamaah-TEST-01',
            'name'   => 'Test Jamaah',
            'slug'   => 'test-jamaah',
            'status' => 'active',
        ];

        $entity = Jamaah::fromArray($data);
        $this->assertSame(1, $entity->id);
        $this->assertSame('Jamaah-TEST-01', $entity->code);
        $this->assertTrue($entity->isActive());
    }

    public function testCreateAndUpdateDTO(): void
    {
        $createInput = [
            'code' => '  Jamaah-DTO-01  ',
            'name' => '  Sample Jamaah  ',
            'slug' => '  sample-jamaah  ',
        ];

        $createDTO = CreateJamaahDTO::fromArray($createInput);
        $this->assertSame('Jamaah-DTO-01', $createDTO->code);
        $this->assertSame('Sample Jamaah', $createDTO->name);
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
            'code' => 'Jamaah-CRUD-01',
            'name' => 'Jamaah CRUD Test',
            'slug' => 'jamaah-crud-test',
        ]);
        $this->assertSame(1, $newId);

        $found = $service->find($newId);
        $this->assertNotNull($found);

        $updated = $service->update($newId, ['name' => 'Updated Jamaah']);
        $this->assertSame('Updated Jamaah', $updated['name']);

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
                'code' => 'FAIL',
                'name' => 'Fail',
                'slug' => 'fail',
            ]);
            $this->fail('Expected Exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertTrue($txBegan);
            $this->assertTrue($txRolledBack);
        }
    }
}
