<?php

namespace App\Core\Exceptions;

/**
 * Class ValidationException
 *
 * Exception yang dilempar ketika validasi input gagal.
 */
class ValidationException extends DomainException
{
    public function __construct(string $message = 'Validation Failed', mixed $errors = null, int $code = 422)
    {
        parent::__construct($message, $errors, $code);
    }
}
