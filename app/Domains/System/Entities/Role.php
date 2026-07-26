<?php

namespace App\Domains\System\Entities;

/**
 * Class Role
 *
 * Domain Entity representasi Role / Peran pengguna.
 */
class Role
{
    public int|string|null $id = null;
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function __construct(
        int|string|null $id = null,
        string $name = '',
        string $slug = '',
        string $description = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
    }
}
