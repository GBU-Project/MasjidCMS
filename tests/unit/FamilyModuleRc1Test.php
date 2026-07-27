<?php

namespace Tests\Unit;

use App\Core\Audit\AuditEntry;
use App\Core\Audit\Config\AuditConfig;
use App\Core\Audit\Listeners\AuditEventListener;
use App\Core\Audit\Services\AuditService;
use App\Core\Contracts\Audit\AuditRepositoryInterface;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Events\EventDispatcher;
use App\Core\Exceptions\ValidationException;

use App\Domains\Family\Controllers\FamilyController;
use App\Domains\Family\DTO\CreateFamilyDTO;
use App\Domains\Family\DTO\UpdateFamilyDTO;
use App\Domains\Family\Entities\Family;
use App\Domains\Family\Repositories\FamilyRepository;
use App\Domains\Family\Services\FamilyService;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

class FamilyModuleRc1Test extends CIUnitTestCase
{
    public function testFamilyEntityStatusHelpers(): void
    {
        $activeFamily = Family::fromArray(['family_no' => 'KK-01', 'family_status' => 'ACTIVE']);
        $this->assertTrue($activeFamily->isActive());
        $this->assertFalse($activeFamily->isMoved());

        $movedFamily = Family::fromArray(['family_no' => 'KK-02', 'family_status' => 'MOVED']);
        $this->assertTrue($movedFamily->isMoved());
    }

    public function testCreateAndUpdateDTO(): void
    {
        $createInput = [
            'family_no' => '  KK-2026-001  ',
            'kk_number' => '3271011503850001',
            'name'      => '  Keluarga Test  ',
            'status'    => 'active',
        ];

        $createDTO = CreateFamilyDTO::fromArray($createInput);
        $this->assertSame('KK-2026-001', $createDTO->family_no);
        $this->assertSame('Keluarga Test', $createDTO->name);
        $this->assertSame('ACTIVE', $createDTO->family_status);

        $updateInput = ['name' => 'Keluarga Updated'];
        $updateDTO = UpdateFamilyDTO::fromArray($updateInput);
        $this->assertSame('Keluarga Updated', $updateDTO->name);
    }

    public function testValidationRules(): void
    {
        $mockRepo = $this->createMock(FamilyRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(false); // Simulate duplicate family_no

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);

        $service = new FamilyService($mockRepo, $mockDispatcher, $mockTxManager);

        $this->expectException(ValidationException::class);
        $service->create([
            'family_no' => 'DUPLICATE-KK',
            'name'      => 'Keluarga Fail',
        ]);
    }

    public function testCrudLifecycleWithUuid(): void
    {
        $store = [];

        $mockRepo = $this->createMock(FamilyRepository::class);
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

        $service = new FamilyService($mockRepo, $mockDispatcher, $mockTxManager);

        // CREATE
        $uuid = $service->create([
            'family_no' => 'KK-UUID-01',
            'name'      => 'Keluarga UUID Test',
        ]);

        $this->assertIsString($uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid);

        // READ
        $found = $service->find($uuid);
        $this->assertNotNull($found);

        // UPDATE
        $updated = $service->update($uuid, ['name' => 'Updated Keluarga UUID']);
        $this->assertSame('Updated Keluarga UUID', $updated['name']);

        // DELETE
        $deleted = $service->delete($uuid);
        $this->assertTrue($deleted);
    }

    public function testFamilyMembersAndHeadTransfer(): void
    {
        $familyStore = [
            'f-1' => [
                'id'             => 'f-1',
                'family_no'      => 'KK-HEAD-01',
                'name'           => 'Keluarga Transfer',
                'head_jamaah_id' => 'head-old',
            ]
        ];

        $membersStore = [
            ['id' => 'head-old', 'full_name' => 'Old Head', 'family_id' => 'f-1', 'family_relation_type' => 'HEAD'],
            ['id' => 'head-new', 'full_name' => 'New Head', 'family_id' => 'f-1', 'family_relation_type' => 'CHILD'],
        ];

        $mockRepo = $this->createMock(FamilyRepository::class);
        $mockRepo->method('exists')->with('f-1')->willReturn(true);
        $mockRepo->method('find')->willReturnCallback(function ($id) use (&$familyStore) {
            return $familyStore[$id] ?? null;
        });
        $mockRepo->method('findMembers')->with('f-1')->willReturn($membersStore);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('update')->willReturnCallback(function ($id, $data) use (&$familyStore) {
            $familyStore[$id] = array_merge($familyStore[$id], $data);
            return true;
        });

        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);

        $service = new FamilyService($mockRepo, $mockDispatcher, $mockTxManager);

        $members = $service->getFamilyMembers('f-1');
        $this->assertCount(2, $members);

        $updatedFamily = $service->transferHead('f-1', 'head-new');
        $this->assertSame('head-new', $updatedFamily['head_jamaah_id']);
    }

    public function testTransactionBoundaryAndRollback(): void
    {
        $mockRepo = $this->createMock(FamilyRepository::class);
        $mockRepo->method('isUniqueExcept')->willReturn(true);
        $mockRepo->method('create')->willThrowException(new \RuntimeException('DB Transaction Error'));

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

        $service = new FamilyService($mockRepo, $mockDispatcher, $mockTxManager);

        try {
            $service->create([
                'family_no' => 'KK-FAIL',
                'name'      => 'Keluarga Fail',
            ]);
            $this->fail('Expected Exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertTrue($txBegan);
            $this->assertTrue($txRolledBack);
        }
    }

    public function testControllerResponseHandling(): void
    {
        $mockService = $this->createMock(FamilyService::class);
        $mockService->method('find')->with('f-100')->willReturn([
            'id'        => 'f-100',
            'family_no' => 'KK-CTRL',
            'name'      => 'Family Controller Test',
        ]);

        $controller = new FamilyController($mockService);

        $config = new \Config\App();
        $uri = new SiteURI($config);
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $response = new Response($config);
        $logger = \Config\Services::logger();

        $controller->initController($request, $response, $logger);

        $httpResponse = $controller->show('f-100');
        $this->assertSame(200, $httpResponse->getStatusCode());

        $body = json_decode($httpResponse->getBody(), true);
        $this->assertSame('success', $body['status']);
        $this->assertSame('KK-CTRL', $body['data']['family_no']);
    }
}
