<?php 

namespace SkankyDev\Form\Fields;

use SkankyDev\Form\FormField;


class DateTimeFields extends FormField {
	protected string $type = 'datetime';
	protected string $viewHtml = 'fields.default';
}
