<?php

namespace App\Domains\Masjid\Services;

use App\Core\CRUD\CrudService;
use App\Core\Contracts\Events\EventDispatcherInterface;
use App\Core\Contracts\Transactions\TransactionManagerInterface;
use App\Core\Contracts\Transactions\UnitOfWorkInterface;
use App\Core\Contracts\Validation\ValidatorInterface;
use App\Core\Exceptions\ValidationException;
use App\Core\Validation\Rules\EmailRule;
use App\Core\Validation\Rules\LengthRule;
use App\Core\Validation\Rules\RequiredRule;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\UrlRule;
use App\Core\Validation\Validator;
use App\Domains\Masjid\Entities\Masjid;
use App\Domains\Masjid\Repositories\MasjidRepository;

/**
 * Class MasjidService
 *
 * Service domain Masjid mengimplementasikan CrudService.
 * Eksekusi: Validation -> UnitOfWork -> Repository -> Commit -> Domain Event -> Audit.
 */
class MasjidService extends CrudService
{
    protected string $entityName = 'Masjid';

    public function __construct(
        ?MasjidRepository $repository = null,
        ?EventDispatcherInterface $dispatcher = null,
        ?TransactionManagerInterface $transactionManager = null,
        ?UnitOfWorkInterface $unitOfWork = null,
        ?ValidatorInterface $validator = null
    ) {
        $repo = $repository ?? new MasjidRepository();
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
                  ->addRule('code', new LengthRule(1, 20))
                  ->addRule('name', new RequiredRule())
                  ->addRule('name', new StringRule())
                  ->addRule('name', new LengthRule(1, 200))
                  ->addRule('slug', new RequiredRule())
                  ->addRule('slug', new StringRule());

        if (!empty($data['email'])) {
            $validator->addRule('email', new EmailRule());
        }

        if (!empty($data['website'])) {
            $validator->addRule('website', new UrlRule());
        }

        $this->validateWith($data, $validator);

        // Unique validations via repository
        /** @var MasjidRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['code']) && !$repo->isUniqueExcept('code', $data['code'])) {
            $errors['code'][] = sprintf('Masjid code [%s] already exists.', $data['code']);
        }

        if (!empty($data['slug']) && !$repo->isUniqueExcept('slug', $data['slug'])) {
            $errors['slug'][] = sprintf('Masjid slug [%s] already exists.', $data['slug']);
        }

        if (!empty($data['email']) && !$repo->isUniqueExcept('email', $data['email'])) {
            $errors['email'][] = sprintf('Masjid email [%s] already exists.', $data['email']);
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
                      ->addRule('code', new LengthRule(1, 20));
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

        if (!empty($data['email'])) {
            $validator->addRule('email', new EmailRule());
        }

        if (!empty($data['website'])) {
            $validator->addRule('website', new UrlRule());
        }

        $this->validateWith($data, $validator);

        /** @var MasjidRepository $repo */
        $repo = $this->repository;
        $errors = [];

        if (!empty($data['code']) && !$repo->isUniqueExcept('code', $data['code'], $id)) {
            $errors['code'][] = sprintf('Masjid code [%s] already exists.', $data['code']);
        }

        if (!empty($data['slug']) && !$repo->isUniqueExcept('slug', $data['slug'], $id)) {
            $errors['slug'][] = sprintf('Masjid slug [%s] already exists.', $data['slug']);
        }

        if (!empty($data['email']) && !$repo->isUniqueExcept('email', $data['email'], $id)) {
            $errors['email'][] = sprintf('Masjid email [%s] already exists.', $data['email']);
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }
}
