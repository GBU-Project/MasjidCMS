<?php

namespace App\Core\Validation\Rules;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Validation\ValidationResult;

/**
 * Class LengthRule
 *
 * Aturan validasi batasan panjang string (min & max).
 */
class LengthRule implements ValidationRuleInterface
{
    public function __construct(
        protected ?int $min = null,
        protected ?int $max = null
    ) {}

    public function validate(mixed $value): ValidationResult
    {
        if ($value === null || !is_string($value)) {
            return ValidationResult::success();
        }

        $length = mb_strlen($value);

        if ($this->min !== null && $length < $this->min) {
            return ValidationResult::failure([sprintf('Field must be at least %d characters long.', $this->min)]);
        }

        if ($this->max !== null && $length > $this->max) {
            return ValidationResult::failure([sprintf('Field must not exceed %d characters.', $this->max)]);
        }

        return ValidationResult::success();
    }
}
