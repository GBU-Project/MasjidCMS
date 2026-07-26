<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class EmailRule
 *
 * Aturan validasi memastikan nilai berformat email valid.
 */
class EmailRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return ValidationResult::failure(['Field must be a valid email address.']);
        }

        return ValidationResult::success();
    }
}
