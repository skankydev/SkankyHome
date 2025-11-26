<?php

namespace SkankyDev\Form;

use SkankyDev\Utilities\Traits\HtmlHelper;
use SkankyDev\Utilities\Traits\StringFacility;

abstract class FormField {
    
	use HtmlHelper, StringFacility;

    protected string $viewHtml = 'fields.default';
    protected string $type = 'text';
    protected string $name;
    protected ?string $label = null;
    protected mixed $value = null;
    protected array $labelAttr = [];
    protected array $attributes = [];
    protected array $rules = [];
    protected array $errors = [];
    protected string $id = '';
    
    public function __construct(string $name, array $options = []) {
        $this->name = $name;
        $this->label = $options['label'] ?? $name;
        $this->value = $options['value'] ?? $options['default'] ?? null;
        $this->attributes = $options['attributes'] ?? [];
        $this->labelAttr = $options['labelAttributes'] ?? [];
        $this->rules = $options['rules'] ?? [];
        $this->id = $options['id'] ?? $this->toCap($name,'_');
    }
    
    public function makePath(string $name): string{
		$name = $this->dotToFolder($name);
		$fileName = VIEW_FOLDER.DS.$name.'.php';
		if(!file_exists($fileName)){
			throw new \Exception("the file : {$fileName} does not exist", 601);
		}
		return $fileName;
	}
	
	public function render(): string {
		$path = $this->makePath($this->viewHtml);
		$data = get_object_vars($this);
		extract($data);
		ob_start();
		require $path;
		return ob_get_clean();
	}
    
    public function setValue(mixed $value): self {
        $this->value = $value;
        return $this;
    }
    
    public function getValue(): mixed {
        return $this->value;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function setErrors(array $errors): self {
        $this->errors = $errors;
        return $this;
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    public function getFirstError(): ?string {
        return $this->errors[0] ?? null;
    }
    
    public function getRules(): array {
        return $this->rules;
    }

}