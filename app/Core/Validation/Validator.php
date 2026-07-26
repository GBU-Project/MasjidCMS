<?php

namespace App\Core\Validation;

use App\Core\Contracts\Validation\ValidationRuleInterface;
use App\Core\Contracts\Validation\ValidatorInterface;

/**
 * Class Validator
 *
 * Engine eksekutor aturan validasi yang mampu memproses sekumpulan data key-value.
 */
class Validator implements ValidatorInterface
{
    /**
     * @var array<string, array<ValidationRuleInterface>>
     */
    protected array $rules = [];

    /**
     * Menambahkan aturan validasi pada field tertentu.
     */
    public function addRule(string $field, ValidationRuleInterface $rule): static
    {
        $this->rules[$field][] = $rule;
        return $this;
    }

    /**
     * Menghapus aturan validasi pada field tertentu.
     */
    public function removeRule(string $field): static
    {
        unset($this->rules[$field]);
        return $this;
    }

    /**
     * Memvalidasi sekumpulan data input berdasarkan aturan terdaftar.
     */
    public function validate(array $data): ValidationResult
    {
        $errors = [];
        $warnings = [];
        $messages = [];
        $isValid = true;

        foreach ($this->rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $result = $rule->validate($value);

                if (!$result->isValid()) {
                    $isValid = false;
                    foreach ($result->errors as $err) {
                        $errors[$field][] = $err;
                    }
                }

                if (!empty($result->warnings)) {
                    foreach ($result->warnings as $warn) {
                        $warnings[$field][] = $warn;
                    }
                }

                if (!empty($result->messages)) {
                    foreach ($result->messages as $msg) {
                        $messages[$field][] = $msg;
                    }
                }
            }
        }

        return new ValidationResult(
            valid: $isValid,
            errors: $errors,
            warnings: $warnings,
            messages: $messages
        );
    }
}
