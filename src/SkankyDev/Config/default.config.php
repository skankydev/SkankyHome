<?php 
/**
 * Copyright (c) 2015 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

return [
	'default' => [
		'namespace' => 'App',
		'action'    => 'index'
	],
	'paginator'=>[
		'limit' => 10,
		'page'  => 1,
		'count' => 1,
		'range' => 5,
	],
	'historique' => ['limit'=>10],
	'autoloader' => [
		'Entity'     => 'Model',
		'Collection' => 'Model',
		'Behavior'   => 'Model',
	],
	'skankydev'  => [
		'version'=>'Alpha.0.0.1'
	],
	'debug' => true,
	'class' => [
		'behavior' => [
			'Timed' => \SkankyDev\Model\Behavior\TimedBehavior::class,
		],
		'middleware' => [],
		'fields' => [
			'text'     => \SkankyDev\Form\Fields\TextField::class,
			'textarea' => \SkankyDev\Form\Fields\TextareaField::class,
			'color'    => \SkankyDev\Form\Fields\ColorField::class,
			'number'   => \SkankyDev\Form\Fields\NumberField::class,
			'select'   => \SkankyDev\Form\Fields\SelectField::class,
			'checkbox' => \SkankyDev\Form\Fields\CheckboxField::class,
			'email'    => \SkankyDev\Form\Fields\EmailField::class,
			'radio'    => \SkankyDev\Form\Fields\RadioField::class,
			'date'     => \SkankyDev\Form\Fields\DateField::class,
			'datetime' => \SkankyDev\Form\Fields\DateTimeField::class,
			'password' => \SkankyDev\Form\Fields\PasswordField::class,
			'default'  => \SkankyDev\Form\Fields\TextField::class,
		],
		'rules' => [
			'required'   => \SkankyDev\Validation\Rules\Required::class,
			'email'      => \SkankyDev\Validation\Rules\Email::class,
			'max_length' => \SkankyDev\Validation\Rules\MaxLength::class,
			'min_length' => \SkankyDev\Validation\Rules\MinLength::class,
			'numeric'    => \SkankyDev\Validation\Rules\Numeric::class,
			'max'        => \SkankyDev\Validation\Rules\Max::class,
			'min'        => \SkankyDev\Validation\Rules\Min::class,
			'regex'      => \SkankyDev\Validation\Rules\Regex::class,
			'confirmed'  => \SkankyDev\Validation\Rules\Confirmed::class,
			'same'       => \SkankyDev\Validation\Rules\Same::class,
			'hex_color'  => \SkankyDev\Validation\Rules\HexColor::class,
		],
	],
	'timehelper'=> [
		'format'=>'Y-m-d H:i:s',
		'timezone'=>'UTC'
	],
];
