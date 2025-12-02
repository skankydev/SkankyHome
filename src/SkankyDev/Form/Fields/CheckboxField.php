<?php 
namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class CheckboxField extends FormField {
	protected string $type = 'checkbox';
	protected string $viewHtml = 'fields.checkbox';
}