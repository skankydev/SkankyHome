# FormBuilder & Validation

Référence détaillée. Vue d'ensemble dans [CLAUDE.md](../CLAUDE.md).

## FormBuilder

```php
$this->add('name', 'text', ['label' => 'Nom', 'rules' => ['required']]);
$this->submit('<i class="icon-save"></i> SAVE');
```

Types de champs disponibles : `text`, `textarea`, `number`, `checkbox`, `radio`, `select`, `file`, `icon`, `hidden`.

## Validation

Règles dans les Forms, vérifiées via `$form->validate($input)`.
En cas d'échec : `redirect()->withErrors($form->getErrors())->withInput($input)`.

Règles dispo (config `class.rules`) : `required`, `email`, `numeric`, `min`, `max`, `min_length`, `max_length`, `regex`, `confirmed`, `same`, `hex_color`. Syntaxe : `'rules' => ['required', 'min_length:3', 'max:255']` (params après `:`). Fail-fast : 1ʳᵉ règle qui casse par champ.
