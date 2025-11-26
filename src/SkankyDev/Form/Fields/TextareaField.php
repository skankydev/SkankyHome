<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class TextareaField extends FormField {
	protected string $type = 'textarea';
	protected string $viewHtml = 'fields.textarea';
}