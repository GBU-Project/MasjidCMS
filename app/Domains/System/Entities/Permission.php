<?php

namespace App\Domains\System\Entities;

/**
 * Class Permission
 *
 * Domain Entity representasi Permission / Hak Akses spesifik.
 */
class Permission
{
    public int|string|null $id = null;
    public string $name = '';
    public string $slug = '';
    public string $module = '';
    public string $description = '';

    public function __construct(
        int|string|null $id = null,
        string $name = '',
        string $slug = '',
        string $module = '',
        string $description = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->module = $module;
        $this->description = $description;
    }
}
