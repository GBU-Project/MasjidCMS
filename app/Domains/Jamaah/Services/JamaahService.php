<?php

namespace App\Domains\Jamaah\Services;

use App\Core\CRUD\CrudService;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;
use App\Core\Contracts\Validation\ValidatorInterface;
use App\Core\Exceptions\ValidationException;
use App\Core\Traits\UuidTrait;
use App\Core\Validation\Rules\EmailRule;
use App\Core\Validation\Rules\LengthRule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Validator;
use App\Domains\Jamaah\Repositories\JamaahRepository;

/**
 * Class JamaahService
 *
 * Service domain Jamaah mengimplementasikan CrudService.
 * Eksekusi: Validation -> UnitOfWork -> Repository -> Commit -> Domain Event -> Audit.
 */
class JamaahService extends CrudService
{
    use UuidTrait;

    protected string $entityName = 'Jamaah';

    public function __construct(
        ?JamaahRepository $repository = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?TransactionManagerInterface $transactionManager = null,
        ?UnitOfWorkInterface $unitOfWork = null,
        ?ValidatorInterface $validator = null
    ) {
        $repo = $repository ?? new JamaahRepository();
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

    protected function beforeDelete(int|string $id): void
    {
        $jamaah = $this->find($id);
        if ($jamaah) {
            $relationType = strtoupper($jamaah['family_relation_type'] ?? '');
            if ($relationType === 'HEAD') {
                throw new ValidationException('Validation failed', [
                    'jamaah' => [sprintf('Cannot delete Jamaah [%s] who is Head of Family. Please transfer Head of Family role first.', (string)$id)]
                ]);
            }
        }
    }

    public function searchAndPaginate(
        string $search = '',
        array $filters = [],
        array $sort = [],
        int $page = 1,
        int $perPage = 15
    ): array {
        /** @var JamaahRepository $repo */
        $repo = $this->repository;
        return $repo->searchAndPaginate($search, $filters, $sort, $page, $perPage);
    }

    protected function validateCreate(array $data): void
    {
        $validator = new Validator();
        $validator->addRule('member_no', new RequiredRule())
                  ->addRule('member_no', new StringRule())
                  ->addRule('member_no', new LengthRule(1, 50))
                  ->addRule('nik', new RequiredRule())
                  ->addRule('nik', new StringRule())
                  ->addRule('nik', new LengthRule(1, 20))
                  ->addRule('full_name', new RequiredRule())
                  ->addRule('full_name', new StringRule())
                  ->addRule('full_name', new LengthRule(1, 200));

        if (!empty($data['email'])) {
            $validator->addRule('email', new EmailRule());
        }

        $this->validateWith($data, $validator);

        // Validate Status Enum
        $allowedStatuses = ['ACTIVE', 'INACTIVE', 'MOVED', 'DECEASED'];
        $status = strtoupper($data['status'] ?? 'ACTIVE');
        if (!in_array($status, $allowedStatuses, true)) {
            throw new ValidationException('Validation failed', [
                'status' => [sprintf('Invalid status [%s]. Allowed statuses: %s', $status, implode(', ', $allowedStatuses))]
            ]);
        }

        /** @var JamaahRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['member_no']) && !$repo->isUniqueExcept('member_no', $data['member_no'])) {
            $errors['member_no'][] = sprintf('Member number [%s] already exists.', $data['member_no']);
        }

        if (!empty($data['nik']) && !$repo->isUniqueExcept('nik', $data['nik'])) {
            $errors['nik'][] = sprintf('NIK [%s] already exists.', $data['nik']);
        }

        if (!empty($data['email']) && !$repo->isUniqueExcept('email', $data['email'])) {
            $errors['email'][] = sprintf('Email [%s] already exists.', $data['email']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }

    protected function validateUpdate(int|string $id, array $data): void
    {
        $validator = new Validator();

        if (array_key_exists('member_no', $data)) {
            $validator->addRule('member_no', new RequiredRule())
                      ->addRule('member_no', new StringRule())
                      ->addRule('member_no', new LengthRule(1, 50));
        }

        if (array_key_exists('nik', $data)) {
            $validator->addRule('nik', new RequiredRule())
                      ->addRule('nik', new StringRule())
                      ->addRule('nik', new LengthRule(1, 20));
        }

        if (array_key_exists('full_name', $data)) {
            $validator->addRule('full_name', new RequiredRule())
                      ->addRule('full_name', new StringRule())
                      ->addRule('full_name', new LengthRule(1, 200));
        }

        if (!empty($data['email'])) {
            $validator->addRule('email', new EmailRule());
        }

        $this->validateWith($data, $validator);

        if (array_key_exists('status', $data)) {
            $allowedStatuses = ['ACTIVE', 'INACTIVE', 'MOVED', 'DECEASED'];
            $status = strtoupper($data['status']);
            if (!in_array($status, $allowedStatuses, true)) {
                throw new ValidationException('Validation failed', [
                    'status' => [sprintf('Invalid status [%s]. Allowed statuses: %s', $status, implode(', ', $allowedStatuses))]
                ]);
            }
        }

        /** @var JamaahRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['member_no']) && !$repo->isUniqueExcept('member_no', $data['member_no'], $id)) {
            $errors['member_no'][] = sprintf('Member number [%s] already exists.', $data['member_no']);
        }

        if (!empty($data['nik']) && !$repo->isUniqueExcept('nik', $data['nik'], $id)) {
            $errors['nik'][] = sprintf('NIK [%s] already exists.', $data['nik']);
        }

        if (!empty($data['email']) && !$repo->isUniqueExcept('email', $data['email'], $id)) {
            $errors['email'][] = sprintf('Email [%s] already exists.', $data['email']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }
}
