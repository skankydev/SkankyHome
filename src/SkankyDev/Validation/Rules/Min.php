<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class Min extends Rule {

	public function __construct(protected float $min) {}
	
	public function check(string $field, mixed $value, array $data = []): bool {
		if ($this->isEmpty($value)) {
			return true;
		}
		
		return (float) $value >= $this->min;
	}
	
	public function message(string $field): string {
		return "Le champ {$field} doit être supérieur ou égal à {$this->min}";
	}
	
}