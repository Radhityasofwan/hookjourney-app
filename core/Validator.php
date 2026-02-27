<?php

class Validator {
    private $errors = [];

    // Jalankan validasi
    // Contoh rules: ['email' => 'required|email', 'password' => 'required|min:6']
    public function validate($data, $rules) {
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = isset($data[$field]) ? trim($data[$field]) : null;

            foreach ($rulesArray as $rule) {
                // Rule: required
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->addError($field, "Kolom {$field} wajib diisi.");
                }

                // Rule: email
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Format email tidak valid.");
                }

                // Rule: min:X
                if (strpos($rule, 'min:') === 0 && $value) {
                    $min = (int) explode(':', $rule)[1];
                    if (strlen($value) < $min) {
                        $this->addError($field, "Kolom {$field} minimal {$min} karakter.");
                    }
                }

                // Rule: max:X
                if (strpos($rule, 'max:') === 0 && $value) {
                    $max = (int) explode(':', $rule)[1];
                    if (strlen($value) > $max) {
                        $this->addError($field, "Kolom {$field} maksimal {$max} karakter.");
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function getErrors() {
        return $this->errors;
    }
}