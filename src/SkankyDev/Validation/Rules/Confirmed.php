<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;


class Confirmed extends Rule {

	public function check(string $field, mixed $value, array $data = []): bool {
		$confirmField = $field . '_confirmation';
		$confirmValue = $data[$confirmField] ?? null;
		
		return $value === $confirmValue;
	}
	
	public function message(string $field): string {
		return "La confirmation du champ {$field} ne correspond pas";
	}
	
}