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
	'string' => "''",
	'int'    => '0',
	'float'  => '0',
	'bool'   => 'true',
	'array'  => '[]',
];
// Types sans valeur par défaut littérale possible (objets / non scalaires).
$noLiteralDefault = ['date', 'datetime', 'ObjectId'];
$fkFields = array_filter($this->fields, fn($f) => $f['type'] === 'ObjectId');
?>

namespace App\Model\Document;

use SkankyDev\Model\Document\MasterDocument;
use SkankyDev\Model\Document\Traits\TimedTrait;
use DateTime;
<?php if(!empty($fkFields)): ?>
use MongoDB\BSON\ObjectId;
<?php endif; ?>

class <?= $name ?> extends MasterDocument {

	use TimedTrait;

<?php foreach($this->fields as $field): ?>
<?php
	$phpType     = in_array($field['type'], ['date', 'datetime']) ? 'DateTime' : $field['type'];
	$withDefault = $field['required'] && !in_array($field['type'], $noLiteralDefault) && isset($defaultValue[$field['type']]);
?>
	public <?= $phpType ?> $<?= $field['name'] ?><?= $withDefault ? ' = ' . $defaultValue[$field['type']] : '' ?>;
<?php endforeach; ?>
<?php foreach($fkFields as $field): ?>
<?php
	$related    = $this->fkRelated($field['name']);
	$relatedVar = lcfirst($related);
?>

	/**
	 * <?= $related ?> lié, résolu à la volée via le magic __get ($<?= $singularCamel ?>-><?= $relatedVar ?>).
	 */
	public function get<?= $related ?>(): ?<?= $related ?> {
		if (!isset($this-><?= $field['name'] ?>)) {
			return null;
		}
		return \App\Model\<?= $related ?>Collection::_findById((string) $this-><?= $field['name'] ?>);
	}
<?php endforeach; ?>

}
