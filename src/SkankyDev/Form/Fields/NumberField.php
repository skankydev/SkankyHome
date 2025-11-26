<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class NumberField extends FormField {
	protected string $type = 'number';
	protected string $viewHtml = 'fields.default';
}
