<?php

namespace App\Domains\Family\Policies;

/**
 * Class FamilyPolicy
 */
class FamilyPolicy
{
    public function view(object $user, mixed $family): bool
    {
        return true;
    }

    public function create(object $user): bool
    {
        return true;
    }

    public function update(object $user, mixed $family): bool
    {
        return true;
    }

    public function delete(object $user, mixed $family): bool
    {
        return true;
    }
}
