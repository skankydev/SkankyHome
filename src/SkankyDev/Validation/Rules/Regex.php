<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;


class Regex extends Rule {

	public function __construct(protected string $pattern) {}
	
	public function check(string $field, mixed $value, array $data = []): bool {
		if ($this->isEmpty($value)) {
			return true;
		}
		
		return preg_match($this->pattern, (string) $value) === 1;
	}
	
	public function message(string $field): string {
		return "Le format du champ {$field} est invalide";
	}
	
}