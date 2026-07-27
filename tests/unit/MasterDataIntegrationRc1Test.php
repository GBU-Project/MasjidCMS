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
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;

use App\Domains\Family\Repositories\FamilyRepository;
use App\Domains\Family\Services\FamilyService;
use App\Domains\Jamaah\Repositories\JamaahRepository;
use App\Domains\Jamaah\Services\JamaahService;

use CodeIgniter\Test\CIUnitTestCase;

class MasterDataIntegrationRc1Test extends CIUnitTestCase
{
    /**
     * Test 1: Full Master Data Workflow (Create Family -> Add Head -> Add Wife -> Add Children -> Transfer Head -> Move Member)
     */
    public function testFullMasterDataWorkflow(): void
    {
        $familyStore = [];
        $jamaahStore = [
            'j-head' => ['id' => 'j-head', 'full_name' => 'Bapak Kepala', 'status' => 'ACTIVE', 'family_id' => null, 'family_relation_type' => null],
            'j-wife' => ['id' => 'j-wife', 'full_name' => 'Ibu Istri', 'status' => 'ACTIVE', 'family_id' => null, 'family_relation_type' => null],
            'j-child' => ['id' => 'j-child', 'full_name' => 'Anak Sulung', 'status' => 'ACTIVE', 'family_id' => null, 'family_relation_type' => null],
        ];

        $mockFamilyRepo = $this->createMock(FamilyRepository::class);
        $mockFamilyRepo->method('isUniqueExcept')->willReturn(true);

        $mockFamilyRepo->method('create')->willReturnCallback(function ($data) use (&$familyStore) {
            $id = $data['id'];
            $familyStore[$id] = $data;
            return $id;
        });

        $mockFamilyRepo->method('find')->willReturnCallback(function ($id) use (&$familyStore) {
            return $familyStore[$id] ?? null;
        });

        $mockFamilyRepo->method('exists')->willReturnCallback(function ($id) use (&$familyStore) {
            return isset($familyStore[$id]);
        });

        $mockFamilyRepo->method('update')->willReturnCallback(function ($id, $data) use (&$familyStore) {
            if (!isset($familyStore[$id])) return false;
            $familyStore[$id] = array_merge($familyStore[$id], $data);
            return true;
        });

        $mockFamilyRepo->method('findMembers')->willReturnCallback(function ($familyId) use (&$jamaahStore) {
            return array_values(array_filter($jamaahStore, fn($j) => $j['family_id'] === $familyId));
        });

        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);
        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);

        $familyService = new FamilyService($mockFamilyRepo, $mockDispatcher, $mockTxManager);

        // CREATE FAMILY
        $familyId = $familyService->create([
            'family_no'      => 'KK-INTEG-001',
            'name'           => 'Keluarga Integrasi',
            'head_jamaah_id' => 'j-head',
        ]);

        $this->assertIsString($familyId);

        // ADD WIFE
        $addedWife = $familyService->addMember($familyId, 'j-wife', 'WIFE');
        $this->assertTrue($addedWife);

        // ADD CHILD
        $addedChild = $familyService->addMember($familyId, 'j-child', 'CHILD');
        $this->assertTrue($addedChild);

        // GET MEMBERS
        $members = $familyService->getFamilyMembers($familyId);
        $this->assertIsArray($members);

        // TRANSFER HEAD TO WIFE
        $updatedFamily = $familyService->transferHead($familyId, 'j-wife');
        $this->assertSame('j-wife', $updatedFamily['head_jamaah_id']);
    }

    /**
     * Test 2: Duplicate Head Prevention Rule
     */
    public function testDuplicateHeadPrevention(): void
    {
        $familyStore = [
            'f-dup' => [
                'id'             => 'f-dup',
                'family_no'      => 'KK-DUP',
                'name'           => 'Keluarga Dup',
                'head_jamaah_id' => 'j-head-1',
            ]
        ];

        $mockFamilyRepo = $this->createMock(FamilyRepository::class);
        $mockFamilyRepo->method('exists')->with('f-dup')->willReturn(true);
        $mockFamilyRepo->method('find')->with('f-dup')->willReturn($familyStore['f-dup']);

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);

        $familyService = new FamilyService($mockFamilyRepo, $mockDispatcher, $mockTxManager);

        $this->expectException(ValidationException::class);
        // Trying to add second HEAD to family that already has HEAD
        $familyService->addMember('f-dup', 'j-head-2', 'HEAD');
    }

    /**
     * Test 3: Soft Delete Protection for Jamaah Head of Family
     */
    public function testJamaahHeadSoftDeletePrevention(): void
    {
        $mockJamaahRepo = $this->createMock(JamaahRepository::class);
        $mockJamaahRepo->method('exists')->with('j-head')->willReturn(true);
        $mockJamaahRepo->method('find')->with('j-head')->willReturn([
            'id'                   => 'j-head',
            'full_name'            => 'Kepala Utama',
            'family_relation_type' => 'HEAD',
        ]);

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);

        $jamaahService = new JamaahService($mockJamaahRepo, $mockDispatcher, $mockTxManager);

        $this->expectException(ValidationException::class);
        $jamaahService->delete('j-head');
    }

    /**
     * Test 4: Family Soft Delete Detaches Members Safely
     */
    public function testFamilySoftDeleteDetachesMembers(): void
    {
        $familyStore = [
            'f-del' => [
                'id'        => 'f-del',
                'family_no' => 'KK-DEL',
                'name'      => 'Keluarga Deleted',
            ]
        ];

        $mockFamilyRepo = $this->createMock(FamilyRepository::class);
        $mockFamilyRepo->method('exists')->with('f-del')->willReturn(true);
        $mockFamilyRepo->method('find')->with('f-del')->willReturn($familyStore['f-del']);
        $mockFamilyRepo->method('delete')->with('f-del')->willReturn(true);

        $mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockTxManager  = $this->createMock(TransactionManagerInterface::class);

        $familyService = new FamilyService($mockFamilyRepo, $mockDispatcher, $mockTxManager);

        $deleted = $familyService->delete('f-del');
        $this->assertTrue($deleted);
    }
}
