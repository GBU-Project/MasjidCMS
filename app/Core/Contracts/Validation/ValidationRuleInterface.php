<?php

namespace App\Core\Contracts\Validation;

use App\Core\Validation\ValidationResult;

/**
 * Interface ValidationRuleInterface
 *
 * Kontrak untuk aturan validasi individu pada Validation Engine.
 */
interface ValidationRuleInterface
{
    /**
     * Memvalidasi satu nilai input.
     *
     * @param mixed $value
     * @return ValidationResult
     */
    public function validate(mixed $value): ValidationResult;
}
