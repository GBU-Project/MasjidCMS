<?php

namespace App\Core\Validation;

/**
 * Class ValidationResult
 *
 * Value Object penampung hasil eksekusi validasi.
 */
class ValidationResult
{
    public function __construct(
        public readonly bool $valid = true,
        public readonly array $errors = [],
        public readonly array $warnings = [],
        public readonly array $messages = []
    ) {}

    /**
     * Memeriksa apakah validasi sukses tanpa error.
     */
    public function isValid(): bool
    {
        return $this->valid && empty($this->errors);
    }

    /**
     * Helper pembuat hasil sukses.
     */
    public static function success(array $messages = []): self
    {
        return new self(valid: true, errors: [], warnings: [], messages: $messages);
    }

    /**
     * Helper pembuat hasil gagal.
     */
    public static function failure(array $errors = [], array $warnings = [], array $messages = []): self
    {
        return new self(valid: false, errors: $errors, warnings: $warnings, messages: $messages);
    }
}
