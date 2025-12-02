<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class SelectField extends FormField {
	protected string $type = 'select';
	protected string $viewHtml = 'fields.select';

	protected array $options = [];
	protected $empty = false;

	public function __construct(string $name, array $options = []) {
        // Appeler le constructeur parent
        parent::__construct($name, $options);
        
        // Ton code spécifique à TextField ici si besoin
        $this->options = $options['options'] ?? [];
        $this->empty = $options['empty'] ?? false;
    }

}