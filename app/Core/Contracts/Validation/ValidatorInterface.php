<?php

namespace App\Core\Contracts\Validation;

use App\Core\Validation\ValidationResult;

/**
 * Interface ValidatorInterface
 *
 * Kontrak manajerial untuk runner validasi sekumpulan data.
 */
interface ValidatorInterface
{
    /**
     * Memvalidasi sekumpulan data key-value.
     *
     * @param array $data
     * @return ValidationResult
     */
    public function validate(array $data): ValidationResult;

    /**
     * Menambahkan aturan validasi untuk field tertentu.
     *
     * @param string $field
     * @param ValidationRuleInterface $rule
     * @return static
     */
    public function addRule(string $field, ValidationRuleInterface $rule): static;

    /**
     * Menghapus aturan validasi pada field tertentu.
     *
     * @param string $field
     * @return static
     */
    public function removeRule(string $field): static;
}
