<?php

namespace App\Core\Exceptions;

/**
 * Class AuthorizationException
 *
 * Exception yang dilempar ketika pengguna tidak memiliki hak akses yang sesuai.
 */
class AuthorizationException extends DomainException
{
    public function __construct(string $message = 'Unauthorized Action', mixed $errors = null, int $code = 403)
    {
        parent::__construct($message, $errors, $code);
    }
}
