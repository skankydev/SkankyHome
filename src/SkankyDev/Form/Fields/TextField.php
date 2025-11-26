<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class TextField extends FormField {
	protected string $type = 'text';
	protected string $viewHtml = 'fields.default';
}