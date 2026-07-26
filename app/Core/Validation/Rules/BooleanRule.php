<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class BooleanRule
 *
 * Aturan validasi memastikan nilai bertipe boolean.
 */
class BooleanRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if ($value !== null && !is_bool($value) && filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null) {
            return ValidationResult::failure(['Field must be a boolean.']);
        }

        return ValidationResult::success();
    }
}
