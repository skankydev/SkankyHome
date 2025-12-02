<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class RadioField extends FormField {
	protected string $type = 'radio';
	protected string $viewHtml = 'fields.radio';
	protected array $options = [];

	
	public function __construct(string $name, array $options = []) {
		parent::__construct($name, $options);
		
		// Stocker les options du radio
		$this->options = $options['options'] ?? [];
	}
}