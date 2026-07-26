<?php

namespace App\Domains\System\DTO;

/**
 * Class LoginRequest
 *
 * Data Transfer Object (DTO) untuk membawa payload otentikasi login.
 */
class LoginRequest
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function __construct(string $username = '', string $password = '', bool $remember = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->remember = $remember;
    }
}
