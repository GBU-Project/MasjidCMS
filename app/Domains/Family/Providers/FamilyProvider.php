<?php

namespace App\Domains\Family\Providers;

use App\Domains\Family\Repositories\FamilyRepository;
use App\Domains\Family\Services\FamilyService;

/**
 * Class FamilyProvider
 */
class FamilyProvider
{
    protected ?FamilyService $service = null;

    public function register(): void
    {
        if ($this->service === null) {
            $repository = new FamilyRepository();
            $this->service = new FamilyService($repository);
        }
    }

    public function boot(): void
    {
    }

    public function getService(): FamilyService
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }
}
