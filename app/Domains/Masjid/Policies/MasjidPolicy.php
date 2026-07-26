<?php

namespace App\Domains\Masjid\Policies;

/**
 * Class MasjidPolicy
 *
 * Kebijakan otorisasi modul Domain Masjid.
 */
class MasjidPolicy
{
    public function view(object $user, mixed $masjid): bool
    {
        return true;
    }

    public function create(object $user): bool
    {
        return true;
    }

    public function update(object $user, mixed $masjid): bool
    {
        return true;
    }

    public function delete(object $user, mixed $masjid): bool
    {
        return true;
    }
}
