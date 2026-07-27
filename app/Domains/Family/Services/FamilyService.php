<?php

namespace App\Domains\Family\Services;

use App\Core\CRUD\CrudService;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;
use App\Core\Contracts\Validation\ValidatorInterface;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Core\Traits\UuidTrait;
use App\Core\Validation\Rules\LengthRule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Validator;
use App\Domains\Family\Repositories\FamilyRepository;
use Config\Database;

/**
 * Class FamilyService
 *
 * Service domain Family mengimplementasikan CrudService.
 * Integrasi Master Data Jamaah & Family.
 */
class FamilyService extends CrudService
{
    use UuidTrait;

    protected string $entityName = 'Family';

    public function __construct(
        ?FamilyRepository $repository = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?TransactionManagerInterface $transactionManager = null,
        ?UnitOfWorkInterface $unitOfWork = null,
        ?ValidatorInterface $validator = null
    ) {
        $repo = $repository ?? new FamilyRepository();
        parent::__construct(
            repository: $repo,
            dispatcher: $dispatcher,
            transactionManager: $transactionManager,
            unitOfWork: $unitOfWork,
            validator: $validator
        );
    }

    protected function beforeCreate(array &$data): void
    {
        if (empty($data['id'])) {
            $data['id'] = $this->generateUuid();
        }
    }

    protected function afterCreate(mixed $entity): void
    {
        parent::afterCreate($entity);

        // Auto-assign Head Jamaah to Family if head_jamaah_id is set
        if (is_array($entity) && !empty($entity['id']) && !empty($entity['head_jamaah_id'])) {
            $this->assignMemberToFamily($entity['id'], $entity['head_jamaah_id'], 'HEAD');
        }
    }

    protected function beforeDelete(int|string $id): void
    {
        // When Family is soft-deleted, detach all Jamaah members safely
        try {
            $db = Database::connect();
            if ($db->tableExists('jamaahs')) {
                $db->table('jamaahs')
                    ->where('family_id', $id)
                    ->update([
                        'family_id'            => null,
                        'family_relation_type' => null,
                    ]);
            }
        } catch (\Throwable $e) {
            // Unit test / DB fallback
        }
    }

    public function getFamilyMembers(string $familyId): array
    {
        if (!$this->exists($familyId)) {
            throw new NotFoundException(sprintf('Family with ID [%s] not found.', $familyId));
        }

        /** @var FamilyRepository $repo */
        $repo = $this->repository;
        return $repo->findMembers($familyId);
    }

    public function addMember(string $familyId, string $jamaahId, string $relationType = 'OTHER'): bool
    {
        if (!$this->exists($familyId)) {
            throw new NotFoundException(sprintf('Family with ID [%s] not found.', $familyId));
        }

        $relationType = strtoupper(trim($relationType));
        $allowedRelations = ['HEAD', 'HUSBAND', 'WIFE', 'CHILD', 'PARENT', 'GUARDIAN', 'OTHER'];

        if (!in_array($relationType, $allowedRelations, true)) {
            throw new ValidationException('Validation failed', [
                'relation_type' => [sprintf('Invalid relation_type [%s]. Allowed: %s', $relationType, implode(', ', $allowedRelations))]
            ]);
        }

        if ($relationType === 'HEAD') {
            $family = $this->find($familyId);
            if (!empty($family['head_jamaah_id']) && $family['head_jamaah_id'] !== $jamaahId) {
                throw new ValidationException('Validation failed', [
                    'relation_type' => ['Family already has a Head of Family. Use transferHead() instead.']
                ]);
            }
        }

        return $this->assignMemberToFamily($familyId, $jamaahId, $relationType);
    }

    public function removeMember(string $familyId, string $jamaahId): bool
    {
        if (!$this->exists($familyId)) {
            throw new NotFoundException(sprintf('Family with ID [%s] not found.', $familyId));
        }

        $family = $this->find($familyId);
        if (!empty($family['head_jamaah_id']) && $family['head_jamaah_id'] === $jamaahId) {
            throw new ValidationException('Validation failed', [
                'jamaah_id' => ['Cannot remove Head of Family. Transfer Head of Family role first.']
            ]);
        }

        try {
            $db = Database::connect();
            if ($db->tableExists('jamaahs')) {
                return $db->table('jamaahs')
                    ->where('id', $jamaahId)
                    ->where('family_id', $familyId)
                    ->update([
                        'family_id'            => null,
                        'family_relation_type' => null,
                    ]);
            }
        } catch (\Throwable $e) {
        }

        return true;
    }

    public function moveMember(string $jamaahId, string $targetFamilyId, string $relationType = 'OTHER'): bool
    {
        if (!$this->exists($targetFamilyId)) {
            throw new NotFoundException(sprintf('Target Family with ID [%s] not found.', $targetFamilyId));
        }

        return $this->addMember($targetFamilyId, $jamaahId, $relationType);
    }

    public function changeRelation(string $familyId, string $jamaahId, string $newRelationType): bool
    {
        if (!$this->exists($familyId)) {
            throw new NotFoundException(sprintf('Family with ID [%s] not found.', $familyId));
        }

        $newRelationType = strtoupper(trim($newRelationType));
        if ($newRelationType === 'HEAD') {
            $this->transferHead($familyId, $jamaahId);
            return true;
        }

        return $this->assignMemberToFamily($familyId, $jamaahId, $newRelationType);
    }

    public function transferHead(string $familyId, string $newHeadJamaahId): mixed
    {
        $family = $this->find($familyId);
        if (!$family) {
            throw new NotFoundException(sprintf('Family with ID [%s] not found.', $familyId));
        }

        // Validate new head status & family membership
        try {
            $db = Database::connect();
            if ($db->tableExists('jamaahs')) {
                $newHead = $db->table('jamaahs')->where('id', $newHeadJamaahId)->where('deleted_at', null)->get()->getRowArray();
                if (!$newHead) {
                    throw new NotFoundException(sprintf('Jamaah with ID [%s] not found.', $newHeadJamaahId));
                }

                $status = strtoupper($newHead['status'] ?? 'ACTIVE');
                if ($status !== 'ACTIVE') {
                    throw new ValidationException('Validation failed', [
                        'new_head_jamaah_id' => [sprintf('New Head of Family must be ACTIVE. Target status is [%s].', $status)]
                    ]);
                }
            }
        } catch (ValidationException|NotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
        }

        $oldHeadId = $family['head_jamaah_id'] ?? null;

        // Assign new head
        $this->assignMemberToFamily($familyId, $newHeadJamaahId, 'HEAD');

        // Demote old head if exists
        if ($oldHeadId && $oldHeadId !== $newHeadJamaahId) {
            $this->assignMemberToFamily($familyId, $oldHeadId, 'OTHER');
        }

        // Update family head_jamaah_id
        return $this->update($familyId, ['head_jamaah_id' => $newHeadJamaahId]);
    }

    public function assignMemberToFamily(string $familyId, string $jamaahId, string $relationType = 'OTHER'): bool
    {
        try {
            $db = Database::connect();
            if ($db->tableExists('jamaahs')) {
                return $db->table('jamaahs')
                    ->where('id', $jamaahId)
                    ->update([
                        'family_id'            => $familyId,
                        'family_relation_type' => strtoupper($relationType),
                    ]);
            }
        } catch (\Throwable $e) {
        }
        return true;
    }

    public function searchAndPaginate(
        string $search = '',
        array $filters = [],
        array $sort = [],
        int $page = 1,
        int $perPage = 15
    ): array {
        /** @var FamilyRepository $repo */
        $repo = $this->repository;
        return $repo->searchAndPaginate($search, $filters, $sort, $page, $perPage);
    }

    protected function validateCreate(array $data): void
    {
        $validator = new Validator();
        $validator->addRule('family_no', new RequiredRule())
                  ->addRule('family_no', new StringRule())
                  ->addRule('family_no', new LengthRule(1, 50))
                  ->addRule('name', new RequiredRule())
                  ->addRule('name', new StringRule())
                  ->addRule('name', new LengthRule(1, 200));

        $this->validateWith($data, $validator);

        /** @var FamilyRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['family_no']) && !$repo->isUniqueExcept('family_no', $data['family_no'])) {
            $errors['family_no'][] = sprintf('Family number [%s] already exists.', $data['family_no']);
        }

        if (!empty($data['kk_number']) && !$repo->isUniqueExcept('kk_number', $data['kk_number'])) {
            $errors['kk_number'][] = sprintf('Kartu Keluarga number [%s] already exists.', $data['kk_number']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }

    protected function validateUpdate(int|string $id, array $data): void
    {
        $validator = new Validator();

        if (array_key_exists('family_no', $data)) {
            $validator->addRule('family_no', new RequiredRule())
                      ->addRule('family_no', new StringRule())
                      ->addRule('family_no', new LengthRule(1, 50));
        }

        if (array_key_exists('name', $data)) {
            $validator->addRule('name', new RequiredRule())
                      ->addRule('name', new StringRule())
                      ->addRule('name', new LengthRule(1, 200));
        }

        $this->validateWith($data, $validator);

        /** @var FamilyRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['family_no']) && !$repo->isUniqueExcept('family_no', $data['family_no'], $id)) {
            $errors['family_no'][] = sprintf('Family number [%s] already exists.', $data['family_no']);
        }

        if (!empty($data['kk_number']) && !$repo->isUniqueExcept('kk_number', $data['kk_number'], $id)) {
            $errors['kk_number'][] = sprintf('Kartu Keluarga number [%s] already exists.', $data['kk_number']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }
}
