%?php
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

<?php 
$defaultValue = [
	'string' => 'text',
	'text' => 'textarea',
	'int', 'float' => 'number',
	'bool' => 'checkbox',
	'date' => 'datetime',
	'datetime' => 'datetime',
	'array' => 'textarea',
];
?>
namespace App\Form;

use SkankyDev\Form\FormBuilder;

class <?= $name ?>Form extends FormBuilder {
	
	public function build() : void {
		
<?php foreach($this->fields as $field): ?>
		$this->add('<?= $field['name'] ?>','<?= $defaultValue[$field['type']] ?? 'text' ?>',[
			'label' => '<?= ucfirst($field['name']) ?>',
			'rules' => [<?= $field['required'] ? "'required'" : '' ?>]
		]);
<?php endforeach; ?>

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
