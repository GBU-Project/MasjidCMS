<?php

namespace App\Domains\Jamaah\Policies;

/**
 * Class JamaahPolicy
 *
 * Kebijakan otorisasi modul Domain Jamaah.
 */
class JamaahPolicy
{
    public function view(object $user, mixed $$jamaah): bool
    {
        return true;
    }

    public function create(object $user): bool
    {
        return true;
    }

    public function update(object $user, mixed $$jamaah): bool
    {
        return true;
    }

    public function delete(object $user, mixed $$jamaah): bool
    {
        return true;
    }
}
