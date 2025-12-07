<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class Email extends Rule {
	
	public function check(string $field, mixed $value, array $data = []): bool {

		if ($this->isEmpty($value)) {
			return true;
		}
		
		return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	public function message(string $field): string {
		return "Le champ {$field} doit être un email valide";
	}

}