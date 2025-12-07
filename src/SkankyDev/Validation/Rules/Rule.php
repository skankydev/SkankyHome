<?php 

namespace SkankyDev\Validation\Rules;

abstract class Rule {
	
	abstract public function check(string $field, mixed $value, array $data = []): bool;
	abstract public function message(string $field): string;

	protected function getValue(string $field, array $data): mixed {
        return $data[$field] ?? null;
    }

	protected function isEmpty(mixed $value): bool {
        if (is_null($value)) {
            return true;
        }
        
        if (is_string($value) && trim($value) === '') {
            return true;
        }
        
        if (is_array($value) && empty($value)) {
            return true;
        }
        
        return false;
    }

}