<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class EmailField extends FormField {
	protected string $type = 'email';
	protected string $viewHtml = 'fields.default';
}