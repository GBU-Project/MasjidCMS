<?php

namespace Tests\Unit;

use App\Core\Audit\AuditEntry;
use App\Core\Audit\Config\AuditConfig;
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

class JamaahModuleRc1Test extends CIUnitTestCase
{
    /**
     * Test 1: Verify Entity status helpers
     */
    public function testJamaahEntityStatusHelpers(): void
    {
        $activeJamaah = Jamaah::fromArray(['member_no' => 'JM-01', 'status' => 'ACTIVE']);
        $this->assertTrue($activeJamaah->isActive());
        $this->assertFalse($activeJamaah->isMoved());

        $movedJamaah = Jamaah::fromArray(['member_no' => 'JM-02', 'status' => 'MOVED']);
        $this->assertTrue($movedJamaah->isMoved());
        $this->assertFalse($movedJamaah->isActive());

        $deceasedJamaah = Jamaah::fromArray(['member_no' => 'JM-03', 'status' => 'DECEASED']);
        $this->assertTrue($deceasedJamaah->isDeceased());
    }

    /**
     * Test 2: Verify DTO parsing and transformations
     */
    public function testCreateAndUpdateDTO(): void
    {
        $createInput = [
            'member_no' => '  JM-2026-999  ',
            'nik'       => '3271011122330001',
            'full_name' => '  Budi Santoso  ',
            'gender'    => 'MALE',
            'status'    => 'active',
        ];

        $createDTO = CreateJamaahDTO::fromArray($createInput);
        $this->assertSame('JM-2026-999', $createDTO->member_no);
        $this->assertSame('Budi Santoso', $createDTO->full_name);
        $this->assertSame('male', $createDTO->gender);
        $this->assertSame('ACTIVE', $createDTO->status);

        $updateInput = ['status' => 'moved'];
        $updateDTO = UpdateJamaahDTO::fromArray($updateInput);
        $this->assertSame('MOVED', $updateDTO->status);
    }

    /**
     * Test 3: Verify Pre-transaction Validation Rules (Unique member_no, nik, email & status enum)
     */
    public function testValidationRules(): void
    {
        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(false); // Simulate duplicate member_no

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);

        $service = new JamaahService($mockRepo, $mockDispatcher, $mockTxManager);

        $this->expectException(ValidationException::class);
        $service->create([
            'member_no' => 'DUPLICATE-JM',
            'nik'       => '3271011122330001',
            'full_name' => 'Budi Santoso',
            'status'    => 'INVALID_STATUS',
        ]);
    }

    /**
     * Test 4: Verify CRUD Lifecycle with UUID Generation
     */
    public function testCrudLifecycleWithUuid(): void
    {
        $store = [];

        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);

        $mockRepo->method('create')->willReturnCallback(function ($data) use (&$store) {
            $id = $data['id'];
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

        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);

        $service = new JamaahService($mockRepo, $mockDispatcher, $mockTxManager);

        // CREATE
        $uuid = $service->create([
            'member_no' => 'JM-UUID-01',
            'nik'       => '3271011122330009',
            'full_name' => 'Jamaah UUID Test',
            'status'    => 'ACTIVE',
        ]);

        $this->assertIsString($uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid);

        // READ
        $found = $service->find($uuid);
        $this->assertNotNull($found);
        $this->assertSame('Jamaah UUID Test', $found['full_name']);

        // UPDATE
        $updated = $service->update($uuid, ['full_name' => 'Updated Jamaah UUID Test']);
        $this->assertSame('Updated Jamaah UUID Test', $updated['full_name']);

        // DELETE
        $deleted = $service->delete($uuid);
        $this->assertTrue($deleted);
    }

    /**
     * Test 5: Verify Transaction Boundary and Rollback
     */
    public function testTransactionBoundaryAndRollback(): void
    {
        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willThrowException(new \RuntimeException('DB Transaction Failed'));

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
                'member_no' => 'JM-FAIL',
                'nik'       => '3271011122330005',
                'full_name' => 'Fail Jamaah',
            ]);
            $this->fail('Expected Exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertTrue($txBegan);
            $this->assertTrue($txRolledBack);
        }
    }

    /**
     * Test 6: Verify Core Regression Pipeline (Validation -> Tx -> Repo -> Commit -> Generic Event -> Audit)
     */
    public function testCoreRegressionPipeline(): void
    {
        $mockRepo = $this->createMock(JamaahRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willReturn('test-uuid-999');

        $mockAuditRepo = $this->createMock(AuditRepositoryInterface::class);
        $loggedAudit = false;
        $mockAuditRepo->method('store')->willReturnCallback(function (AuditEntry $entry) use (&$loggedAudit) {
            $loggedAudit = true;
            return true;
        });

        $auditConfig = new AuditConfig();
        $auditConfig->storeUserAgent = false;
        $auditConfig->storeIPAddress = false;

        $auditService = new AuditService($mockAuditRepo, $auditConfig);
        $auditListener = new AuditEventListener($auditService);

        $dispatcher = new EventDispatcher(registerDefaultListeners: false);
        $dispatcher->listen('*', [$auditListener, 'handle']);

        $txCommitted = false;
        $mockTxManager = $this->createMock(TransactionManagerInterface::class);
        $mockTxManager->method('commit')->willReturnCallback(function () use (&$txCommitted) {
            $txCommitted = true;
        });

        $service = new JamaahService($mockRepo, $dispatcher, $mockTxManager);

        $resultId = $service->create([
            'member_no' => 'JM-REG-01',
            'nik'       => '3271011122339999',
            'full_name' => 'Jamaah Regression Test',
        ]);

        $this->assertSame('test-uuid-999', $resultId);
        $this->assertTrue($txCommitted);
        $this->assertTrue($loggedAudit);
    }

    /**
     * Test 7: Verify Controller HTTP Response Handling
     */
    public function testControllerResponseHandling(): void
    {
        $mockService = $this->createMock(JamaahService::class);
        $mockService->method('find')->with('uuid-123')->willReturn([
            'id'        => 'uuid-123',
            'member_no' => 'JM-CTRL',
            'nik'       => '3271011122338888',
            'full_name' => 'Jamaah Controller Test',
            'status'    => 'ACTIVE',
        ]);

        $controller = new JamaahController($mockService);

        $config = new \Config\App();
        $uri = new SiteURI($config);
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $response = new Response($config);
        $logger = \Config\Services::logger();

        $controller->initController($request, $response, $logger);

        $httpResponse = $controller->show('uuid-123');
        $this->assertSame(200, $httpResponse->getStatusCode());

        $body = json_decode($httpResponse->getBody(), true);
        $this->assertSame('success', $body['status']);
        $this->assertSame('JM-CTRL', $body['data']['member_no']);
    }
}
