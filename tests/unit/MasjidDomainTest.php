<?php

namespace Tests\Unit;

use App\Core\Audit\AuditEntry;
use App\Core\Audit\Listeners\AuditListener;
use App\Core\Contracts\Audit\AuditRepositoryInterface;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Events\EntityCreatedEvent;
use App\Core\Events\EntityDeletedEvent;
use App\Core\Events\EntityUpdatedEvent;
use App\Core\Events\EventDispatcher;
use App\Core\Exceptions\ValidationException;

use App\Domains\Masjid\Controllers\MasjidController;
use App\Domains\Masjid\DTO\CreateMasjidDTO;
use App\Domains\Masjid\DTO\UpdateMasjidDTO;
use App\Domains\Masjid\Entities\Masjid;
use App\Domains\Masjid\Repositories\MasjidRepository;
use App\Domains\Masjid\Services\MasjidService;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

class MasjidDomainTest extends CIUnitTestCase
{
    /**
     * Test 1: Verify Entity creation and serialization
     */
    public function testMasjidEntityInstantiation(): void
    {
        $data = [
            'id'          => 1,
            'code'        => 'MSJ-001',
            'name'        => 'Masjid Raya',
            'slug'        => 'masjid-raya',
            'type'        => 'Masjid Raya',
            'email'       => 'info@masjidraya.com',
            'phone'       => '021-123456',
            'website'     => 'https://masjidraya.com',
            'address'     => 'Jl. Raya No. 1',
            'status'      => 'active',
            'created_at'  => '2026-07-27 00:00:00',
        ];

        $entity = Masjid::fromArray($data);
        $this->assertSame(1, $entity->id);
        $this->assertSame('MSJ-001', $entity->code);
        $this->assertTrue($entity->isActive());
        $this->assertFalse($entity->isInactive());
        $this->assertSame('masjid-raya', $entity->slug);

        $serialized = $entity->toArray();
        $this->assertSame('MSJ-001', $serialized['code']);
    }

    /**
     * Test 2: Verify DTO parsing and transformation
     */
    public function testCreateAndUpdateDTO(): void
    {
        $createInput = [
            'code'    => '  MSJ-DTO-01  ',
            'name'    => '  Masjid DTO Test  ',
            'slug'    => '  masjid-dto-test  ',
            'email'   => 'dto@test.com',
            'website' => 'https://test.com',
        ];

        $createDTO = CreateMasjidDTO::fromArray($createInput);
        $this->assertSame('MSJ-DTO-01', $createDTO->code);
        $this->assertSame('Masjid DTO Test', $createDTO->name);
        $this->assertSame('masjid-dto-test', $createDTO->slug);

        $arrayData = $createDTO->toArray();
        $this->assertArrayHasKey('code', $arrayData);
        $this->assertSame('MSJ-DTO-01', $arrayData['code']);

        $updateInput = ['name' => '  Updated Masjid Name  '];
        $updateDTO = UpdateMasjidDTO::fromArray($updateInput);
        $this->assertSame('Updated Masjid Name', $updateDTO->name);
        $this->assertNull($updateDTO->code);
    }

    /**
     * Test 3: Verify Validation Rules (Required, Unique, Email, URL)
     */
    public function testServiceValidationRules(): void
    {
        $mockRepo = $this->createMock(MasjidRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(false); // Simulate duplicate code
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager = $this->createMock(TransactionManagerInterface::class);

        $service = new MasjidService($mockRepo, $mockDispatcher, $mockTxManager);

        $this->expectException(ValidationException::class);
        $service->create([
            'code'  => 'DUPLICATE',
            'name'  => 'Test Masjid',
            'slug'  => 'test-masjid',
            'email' => 'invalid-email',
        ]);
    }

    /**
     * Test 4: Verify CRUD Lifecycle with In-Memory Repository Double
     */
    public function testServiceCrudLifecycle(): void
    {
        $store = [];

        $mockRepo = $this->createMock(MasjidRepository::class);
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

        $service = new MasjidService($mockRepo, $mockDispatcher, $mockTxManager);

        // CREATE
        $createData = [
            'code' => 'MSJ-CRUD-01',
            'name' => 'Masjid CRUD Unit Test',
            'slug' => 'masjid-crud-unit-test',
        ];
        $newId = $service->create($createData);
        $this->assertSame(1, $newId);

        // READ
        $found = $service->find($newId);
        $this->assertNotNull($found);
        $this->assertSame('Masjid CRUD Unit Test', $found['name']);

        // UPDATE
        $updated = $service->update($newId, ['name' => 'Updated Masjid CRUD Unit Test']);
        $this->assertSame('Updated Masjid CRUD Unit Test', $updated['name']);

        // DELETE
        $deleted = $service->delete($newId);
        $this->assertTrue($deleted);
    }

    /**
     * Test 5: Verify Transaction Boundary & Rollback on Exception
     */
    public function testTransactionBoundaryAndRollback(): void
    {
        $mockRepo = $this->createMock(MasjidRepository::class);
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

        $service = new MasjidService($mockRepo, $mockDispatcher, $mockTxManager);

        try {
            $service->create([
                'code' => 'MSJ-FAIL',
                'name' => 'Masjid Fail',
                'slug' => 'masjid-fail',
            ]);
            $this->fail('Expected Exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertSame('DB Error', $e->getMessage());
            $this->assertTrue($txBegan);
            $this->assertTrue($txRolledBack);
        }
    }

    /**
     * Test 6: Verify Generic Event Dispatching (EntityCreatedEvent, EntityUpdatedEvent, EntityDeletedEvent)
     */
    public function testGenericEventDispatching(): void
    {
        $mockRepo = $this->createMock(MasjidRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willReturn(10);
        $mockRepo->method('exists')->willReturn(true);
        $mockRepo->method('find')->willReturn(['id' => 10, 'code' => 'MSJ-EVT', 'name' => 'Evt']);
        $mockRepo->method('update')->willReturn(true);
        $mockRepo->method('delete')->willReturn(true);

        $mockTxManager = $this->createMock(TransactionManagerInterface::class);

        $dispatchedEvents = [];
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockDispatcher->method('dispatch')->willReturnCallback(function ($event) use (&$dispatchedEvents) {
            $dispatchedEvents[] = get_class($event);
        });

        $service = new MasjidService($mockRepo, $mockDispatcher, $mockTxManager);

        $service->create(['code' => 'MSJ-EVT', 'name' => 'Evt', 'slug' => 'evt']);
        $service->update(10, ['name' => 'Updated Evt']);
        $service->delete(10);

        $this->assertCount(3, $dispatchedEvents);
        $this->assertSame(EntityCreatedEvent::class, $dispatchedEvents[0]);
        $this->assertSame(EntityUpdatedEvent::class, $dispatchedEvents[1]);
        $this->assertSame(EntityDeletedEvent::class, $dispatchedEvents[2]);
    }

    /**
     * Test 7: Verify Core Regression Test (Full Pipeline: Validation -> Tx -> Repo -> Commit -> Generic Event -> Audit)
     */
    public function testCoreRegressionPipeline(): void
    {
        $mockRepo = $this->createMock(MasjidRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willReturn(99);

        $mockAuditRepo = $this->createMock(AuditRepositoryInterface::class);
        $loggedAudit = false;
        $mockAuditRepo->method('store')->willReturnCallback(function (AuditEntry $entry) use (&$loggedAudit) {
            $loggedAudit = true;
            return true;
        });

        $auditConfig = new \App\Core\Audit\Config\AuditConfig();
        $auditConfig->storeUserAgent = false;
        $auditConfig->storeIPAddress = false;

        $auditService = new \App\Core\Audit\Services\AuditService($mockAuditRepo, $auditConfig);
        $auditListener = new \App\Core\Audit\Listeners\AuditEventListener($auditService);

        $dispatcher = new EventDispatcher(registerDefaultListeners: false);
        $dispatcher->listen('*', [$auditListener, 'handle']);

        $txCommitted = false;
        $mockTxManager = $this->createMock(TransactionManagerInterface::class);
        $mockTxManager->method('commit')->willReturnCallback(function () use (&$txCommitted) {
            $txCommitted = true;
        });

        $service = new MasjidService($mockRepo, $dispatcher, $mockTxManager);

        $resultId = $service->create([
            'code' => 'MSJ-REG-01',
            'name' => 'Masjid Regression Pipeline',
            'slug' => 'masjid-regression-pipeline',
        ]);

        $this->assertSame(99, $resultId);
        $this->assertTrue($txCommitted, 'Transaction commit must execute');
        $this->assertTrue($loggedAudit, 'Audit listener must record event to audit repository');
    }

    /**
     * Test 8: Verify MasjidController HTTP Response Formats
     */
    public function testMasjidControllerResponseHandling(): void
    {
        $mockService = $this->createMock(MasjidService::class);
        $mockService->method('find')->with(1)->willReturn([
            'id'   => 1,
            'code' => 'MSJ-CTRL',
            'name' => 'Controller Test',
            'slug' => 'controller-test',
        ]);

        $controller = new MasjidController($mockService);

        $config = new \Config\App();
        $uri = new SiteURI($config);
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $response = new Response($config);
        $logger = \Config\Services::logger();

        $controller->initController($request, $response, $logger);

        $httpResponse = $controller->show(1);
        $this->assertSame(200, $httpResponse->getStatusCode());

        $body = json_decode($httpResponse->getBody(), true);
        $this->assertSame('success', $body['status']);
        $this->assertSame('MSJ-CTRL', $body['data']['code']);
    }
}
