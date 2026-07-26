<?php

namespace App\Domains\Jamaah\Providers;

use App\Domains\Jamaah\Repositories\JamaahRepository;
use App\Domains\Jamaah\Services\JamaahService;

/**
 * Class JamaahProvider
 *
 * Provider Lifecycle Manager untuk pendaftaran service & pemicu boot modul Domain Jamaah.
 */
class JamaahProvider
{
    protected ?JamaahService $service = null;

    /**
     * Mendaftarkan dependency / service container domain.
     */
    public function register(): void
    {
        if ($this->service === null) {
            $repository = new JamaahRepository();
            $this->service = new JamaahService($repository);
        }
    }

    /**
     * Memulai pemuatan konfigurasi / event listener domain.
     */
    public function boot(): void
    {
        // Reserved for domain-specific event listener registrations if needed
    }

    public function getService(): JamaahService
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }
}
