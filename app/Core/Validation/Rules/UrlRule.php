<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class UrlRule
 *
 * Aturan validasi memastikan nilai berformat URL valid.
 */
class UrlRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL) === false) {
            return ValidationResult::failure(['Field must be a valid URL.']);
        }

        return ValidationResult::success();
    }
}
