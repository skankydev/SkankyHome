<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class MinLength extends Rule {

    public function __construct(protected int $min) {}
    
    public function check(string $field, mixed $value, array $data = []): bool {
        if ($this->isEmpty($value)) {
            return true;
        }
        
        return mb_strlen((string) $value) >= $this->min;
    }
    
    public function message(string $field): string {
        return "Le champ {$field} doit contenir au moins {$this->min} caractères";
    }

}