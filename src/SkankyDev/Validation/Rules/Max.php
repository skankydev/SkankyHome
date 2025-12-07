<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;


class Max extends Rule {
    public function __construct(protected float $max) {}
    
    public function check(string $field, mixed $value, array $data = []): bool {
        if ($this->isEmpty($value)) {
            return true;
        }
        
        return (float) $value <= $this->max;
    }
    
    public function message(string $field): string {
        return "Le champ {$field} doit être inférieur ou égal à {$this->max}";
    }
}
