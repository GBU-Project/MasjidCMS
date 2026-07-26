<?php

namespace App\Domains\Masjid\Providers;

use App\Domains\Masjid\Repositories\MasjidRepository;
use App\Domains\Masjid\Services\MasjidService;

/**
 * Class MasjidProvider
 *
 * Provider Lifecycle Manager untuk pendaftaran service & pemicu boot modul Domain Masjid.
 */
class MasjidProvider
{
    protected ?MasjidService $service = null;

    /**
     * Mendaftarkan dependency / service container domain.
     */
    public function register(): void
    {
        if ($this->service === null) {
            $repository = new MasjidRepository();
            $this->service = new MasjidService($repository);
        }
    }

    /**
     * Memulai pemuatan konfigurasi / event listener domain.
     */
    public function boot(): void
    {
        // Reserved for domain-specific event listener registrations if needed in future
    }

    public function getService(): MasjidService
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }
}
