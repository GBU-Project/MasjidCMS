<?php

namespace App\Domains\Jamaah\Services;

use App\Core\CRUD\CrudService;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;
use App\Core\Contracts\Validation\ValidatorInterface;
use App\Core\Exceptions\ValidationException;
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

    protected function validateCreate(array $data): void
    {
        $validator = new Validator();
        $validator->addRule('code', new RequiredRule())
                  ->addRule('code', new StringRule())
                  ->addRule('code', new LengthRule(1, 50))
                  ->addRule('name', new RequiredRule())
                  ->addRule('name', new StringRule())
                  ->addRule('name', new LengthRule(1, 200))
                  ->addRule('slug', new RequiredRule())
                  ->addRule('slug', new StringRule());

        $this->validateWith($data, $validator);

        /** @var JamaahRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['code']) && !$repo->isUniqueExcept('code', $data['code'])) {
            $errors['code'][] = sprintf('Jamaah code [%s] already exists.', $data['code']);
        }

        if (!empty($data['slug']) && !$repo->isUniqueExcept('slug', $data['slug'])) {
            $errors['slug'][] = sprintf('Jamaah slug [%s] already exists.', $data['slug']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }

    protected function validateUpdate(int|string $id, array $data): void
    {
        $validator = new Validator();

        if (array_key_exists('code', $data)) {
            $validator->addRule('code', new RequiredRule())
                      ->addRule('code', new StringRule())
                      ->addRule('code', new LengthRule(1, 50));
        }

        if (array_key_exists('name', $data)) {
            $validator->addRule('name', new RequiredRule())
                      ->addRule('name', new StringRule())
                      ->addRule('name', new LengthRule(1, 200));
        }

        if (array_key_exists('slug', $data)) {
            $validator->addRule('slug', new RequiredRule())
                      ->addRule('slug', new StringRule());
        }

        $this->validateWith($data, $validator);

        /** @var JamaahRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['code']) && !$repo->isUniqueExcept('code', $data['code'], $id)) {
            $errors['code'][] = sprintf('Jamaah code [%s] already exists.', $data['code']);
        }

        if (!empty($data['slug']) && !$repo->isUniqueExcept('slug', $data['slug'], $id)) {
            $errors['slug'][] = sprintf('Jamaah slug [%s] already exists.', $data['slug']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }
}
