<?php 
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace App\Form\Fields;

use SkankyDev\Config\Config;
use SkankyDev\Form\FormField;


class IconField extends FormField {
	protected string $type = 'icon';
	protected string $viewHtml = 'fields.icon';

	protected array $icons = [];
	protected $empty = false;

	public function __construct(string $name, array $options = []) {
		// Appeler le constructeur parent
		parent::__construct($name, $options);
		
		
		$this->icons = Config::get('icons') ?? [];
	}
		
}
