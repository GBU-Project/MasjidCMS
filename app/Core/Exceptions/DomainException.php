<?php

namespace App\Core\Exceptions;

use Exception;

/**
 * Class DomainException
 *
 * Base exception untuk seluruh custom exception pada MasjidCMS.
 */
class DomainException extends Exception
{
    /**
     * Data tambahan atau detail error yang dapat dilampirkan.
     *
     * @var mixed
     */
    protected mixed $errors;

    public function __construct(string $message = 'Domain Exception', mixed $errors = null, int $code = 400, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Mendapatkan rincian error.
     *
     * @return mixed
     */
    public function getErrors(): mixed
    {
        return $this->errors;
    }
}
