<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;


class Same extends Rule {

    public function __construct(protected string $otherField) {}
    
    public function check(string $field, mixed $value, array $data = []): bool {
        $otherValue = $this->getValue($this->otherField, $data);
        return $value === $otherValue;
    }
    
    public function message(string $field): string {
        return "Le champ {$field} doit être identique à {$this->otherField}";
    }
    
}