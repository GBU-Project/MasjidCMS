<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class RequiredRule
 *
 * Aturan validasi memastikan nilai tidak boleh null, kosong, atau hanya berisi whitespace.
 */
class RequiredRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
            return ValidationResult::failure(['Field is required and cannot be empty.']);
        }

        return ValidationResult::success();
    }
}
