<?php 

namespace SkankyDev\Form;

use SkankyDev\Config\Config;
use SkankyDev\Http\UrlBuilder;
use SkankyDev\Utilities\Traits\HtmlHelper;
use SkankyDev\Utilities\Traits\StringFacility;
use SkankyDev\Validation\Validator;
use SkankyDev\Utilities\Session;

abstract class FormBuilder {

	use HtmlHelper, StringFacility;
	
	protected string $action = '';
	protected string $method = 'POST';
	protected array $attributes = [];
	protected array $fields = [];
	protected array $fieldTypes = [];
	protected ?string $submitLabel = 'save';
	protected array $submitAttributes = ['type'=>'submit','class'=>'btn-success'];
	protected array $data = [];
	protected array $errors = [];
	protected array $old = [];
	
	public function __construct(array $link = [], string $method = 'POST', array $attributes = []) {
		$this->action = UrlBuilder::_build($link);
		$this->method = strtoupper($method);
		$this->attributes = $attributes;
		$this->fieldTypes = Config::get('class.fields');
		$this->errors = Session::getAndClean('errors') ?? [];
		$this->old = Session::getAndClean('old') ?? [];
		$this->build();
	}
	
	/**
	 * Méthode abstraite à implémenter dans les classes enfants
	 */
	abstract public function build(): void;
	
	/**
	 * Ajouter un champ au formulaire
	 */
	public function add(string $name, string $type, array $options = []): self {
		if (!isset($this->fieldTypes[$type])) {
			throw new \Exception("Type de champ inconnu : {$type}");
		}
		
		$fieldClass = $this->fieldTypes[$type];
		
		if (isset($this->old[$name])) {
			$options['value'] = $this->old[$name];
		}else if (isset($this->data[$name])) {
			$options['value'] = $this->data[$name];
		}
		
		// Ajouter les erreurs si disponibles
		if (isset($this->errors[$name])) {
			$options['errors'] = (array) $this->errors[$name];
		}
		
		$this->fields[$name] = new $fieldClass($name, $options);
		
		return $this;
	}
	
	/**
	 * Définir le bouton submit
	 */
	public function submit(string $label, array $attributes = []): self {
		$this->submitLabel = $label;
		$this->submitAttributes = [...$this->submitAttributes , ...$attributes];
		return $this;
	}
	
	/**
	 * Définir les données du formulaire (pour pré-remplir)
	 */
	public function setData(array $data): self {
		$this->data = $data;
		
		// Mettre à jour les valeurs des champs existants
		foreach ($this->fields as $name => $field) {
			if (isset($data[$name])) {
				$field->setValue($data[$name]);
			}
		}
		
		return $this;
	}
	
	/**
	 * Définir les erreurs de validation
	 */
	public function setErrors(array $errors): self {
		$this->errors = $errors;
		
		// Mettre à jour les erreurs des champs existants
		foreach ($this->fields as $name => $field) {
			if (isset($errors[$name])) {
				$field->setErrors((array) $errors[$name]);
			}
		}
		
		return $this;
	}
	
	/**
	 * Rendre le formulaire complet
	 */
	public function render(): string {
		$html = $this->open();
		
		foreach ($this->fields as $field) {
			$html .= $field->render();
		}
		
		if ($this->submitLabel) {
			$html .= $this->renderSubmit();
		}
		
		$html .= $this->close();
		
		return $html;
	}
	
	/**
	 * Ouvrir la balise form
	 */
	public function open(): string {
		$html = '<form action="' . $this->action . '" method="' . $this->method . '"';
		
		foreach ($this->attributes as $key => $value) {
			$html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
		}
		
		$html .= '>';
		
		// Ajouter le CSRF token si méthode POST
		if ($this->method === 'POST') {
			$html .= csrf_field();
		}
		
		return $html;
	}
	
	/**
	 * Fermer la balise form
	 */
	public function close(): string {
		return '</form>';
	}
	
	/**
	 * Rendre un champ spécifique
	 */
	public function renderField(string $name): string {
		if (!isset($this->fields[$name])) {
			throw new \Exception("Champ introuvable : {$name}");
		}
		
		return $this->fields[$name]->render();
	}
	
	/**
	 * Rendre le bouton submit
	 */
	protected function renderSubmit(): string {
		$html = '<div class="form-group">';
		$html .= $this->surround($this->submitLabel,'button',$this->submitAttributes);
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * Obtenir tous les champs
	 */
	public function getFields(): array {
		return $this->fields;
	}
	
	/**
	 * Valider le formulaire
	 */
	public function validate(array $data): bool {
		$this->setData($data);
		
		// Récupérer les règles depuis les champs
		$rules = [];
		foreach ($this->fields as $name => $field) {
			$fieldRules = $field->getRules();
			if (!empty($fieldRules)) {
				$rules[$name] = $fieldRules;
			}
		}
		
		
		$validator = new Validator($rules, $data);
		
		if (!$validator->validate()) {
			$this->setErrors($validator->errors());
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Obtenir les données validées
	 */
	public function getData(): array {
		return $this->data;
	}
	
	/**
	 * Obtenir les erreurs
	 */
	public function getErrors(): array {
		return $this->errors;
	}
}