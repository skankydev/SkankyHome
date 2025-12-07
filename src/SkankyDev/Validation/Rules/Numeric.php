<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class Numeric extends Rule {

	public function check(string $field, mixed $value, array $data = []): bool {
		if ($this->isEmpty($value)) {
			return true;
		}
		
		return is_numeric($value);
	}
	
	public function message(string $field): string {
		return "Le champ {$field} doit être un nombre";
	}

}