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
$defaultValue =[
	'string' => "''", 
	'int' => '0',
	'float' => '0',
	'bool' => 'true',
	'date' => '',
	'datetime' => '',
	'array' => '[]'
];
 ?>

namespace App\Model\Document;

use SkankyDev\Model\Document\MasterDocument;
use DateTime;

class <?= $name ?> extends MasterDocument {
	
<?php foreach($this->fields as $field): ?>
	public <?= $field['type']=='date' || $field['type']=='datetime' ? 'DateTime': $field['type'] ?> $<?= $field['name'] ?><?= $field['required'] ? ' = ' . $defaultValue[$field['type']] : '' ?>;
<?php endforeach; ?>

}