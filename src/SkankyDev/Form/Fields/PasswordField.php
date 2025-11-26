<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class PasswordField extends FormField {
	protected string $type = 'password';
	protected string $viewHtml = 'fields.default';
}
