<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class IntegerRule
 *
 * Aturan validasi memastikan nilai bertipe integer atau angka bulat.
 */
class IntegerRule implements ValidationRuleInterface
{
    public function validate(mixed $value): ValidationResult
    {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_INT) === false) {
            return ValidationResult::failure(['Field must be an integer.']);
        }

        return ValidationResult::success();
    }
}
