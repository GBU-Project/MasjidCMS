<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class StringRule
 *
 * Aturan validasi memastikan nilai bertipe data string.
 */
class StringRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if ($value !== null && !is_string($value)) {
            return ValidationResult::failure(['Field must be a string.']);
        }

        return ValidationResult::success();
    }
}
