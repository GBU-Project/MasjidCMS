<?php

namespace App\Core\Exceptions;

/**
 * Class NotFoundException
 *
 * Exception yang dilempar ketika entitas atau resource tidak ditemukan.
 */
class NotFoundException extends DomainException
{
    public function __construct(string $message = 'Resource Not Found', mixed $errors = null, int $code = 404)
    {
        parent::__construct($message, $errors, $code);
    }
}
