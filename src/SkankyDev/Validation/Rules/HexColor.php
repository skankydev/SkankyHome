<?php 

namespace SkankyDev\Validation\Rules;

use SkankyDev\Validation\Rules\Rule;

class HexColor implements Rule {

    public function check(string $field, mixed $value, array $data = []): bool {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) === 1;
    }
    
    public function message(string $field): string {
        return "Le champ {$field} doit être une couleur hexadécimale valide";
    }

}
