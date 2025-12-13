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

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;


class Same extends Rule {

    public function __construct(protected string $otherField) {}
    
    public function check(string $field, mixed $value, array $data = []): bool {
        $otherValue = $this->getValue($this->otherField, $data);
        return $value === $otherValue;
    }
    
    public function message(string $field): string {
        return "Le champ {$field} doit être identique à {$this->otherField}";
    }
    
}