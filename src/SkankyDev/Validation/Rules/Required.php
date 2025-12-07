<?php

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class Required extends Rule {
	
	public function check(string $field, mixed $value, array $data = []): bool {
		return !$this->isEmpty($value);
	}
	
	public function message(string $field): string {
		return "Le champ {$field} est requis";
	}
}