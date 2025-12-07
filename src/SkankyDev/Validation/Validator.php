<?php

namespace SkankyDev\Validation;

use SkankyDev\Validation\Rules\Rule;
use SkankyDev\Config\Config;

class Validator {

	protected array $data = [];
	protected array $rules = [];
	protected array $errors = [];
	protected array $ruleRegistry = [];

	public function __construct(array $rules = [],array $data = []) {
		$this->data = $data;
		$this->rules = $rules;

		$this->ruleRegistry = Config::get('class.rules');

	}

	public function setData(array $data): self {
		$this->data = $data;
		return $this;
	}
	
	public function setRules(array $rules): self {
		$this->rules = $rules;
		return $this;
	}

	public function validate(): bool {
		$this->errors = [];
		
		foreach ($this->rules as $field => $rules) {
			$value = $this->data[$field] ?? null;
			$rules = $this->makeRules($rules);
			
			foreach ($rules as $rule) {
				if (!$rule->check($field, $value, $this->data)) {
					$this->errors[$field][] = $rule->message($field);
					break;
				}
			}
		}
		
		return empty($this->errors);
	}

	protected function makeRules(string|array $rules): array {
		if (is_string($rules)) {
			$rules = explode('|', $rules);
		}
		
		$object = [];
		foreach ($rules as $rule) {
			$object[] = $this->createRule($rule);
		}
		
		return $object;
	}

	protected function createRule(string $rule): Rule {
			
		// Parser "min:3" ou "in:1,2,3"
		if (str_contains($rule, ':')) {
			[$name, $params] = explode(':', $rule, 2);
			$params = explode(',', $params);
		} else {
			$name = $rule;
			$params = [];
		}
		
		// Récupérer la classe
		$class = $this->ruleRegistry[$name] ?? null;
		
		if (!$class) {
			throw new \Exception("Règle de validation inconnue : {$name}");
		}
		
		// Instancier avec paramètres
		return new $class(...$params);
	}

	public function errors(): array {
        return $this->errors;
    }
    
    public function error(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }
    
    public function hasError(string $field): bool {
        return isset($this->errors[$field]);
    }

}