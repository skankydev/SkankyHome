<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class SelectField extends FormField {
	protected string $type = 'select';
	protected string $viewHtml = 'fields.select';
}