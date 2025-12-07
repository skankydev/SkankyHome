<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class MaxLength extends Rule {
	
	public function __construct(protected int $max) {}
	
	public function check(string $field, mixed $value, array $data = []): bool {
		if ($this->isEmpty($value)) {
			return true;
		}
		
		return mb_strlen((string) $value) <= $this->max;
	}
	
	public function message(string $field): string {
		return "Le champ {$field} doit contenir maximum {$this->max} caractères";
	}
}