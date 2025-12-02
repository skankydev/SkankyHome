<?php 

namespace App\Form;

use SkankyDev\Form\FormBuilder;

class SequenceForm extends FormBuilder{

	public function build() : void {

		/*'email'
		'radio'
		'date'
		'datetime'
		'password'*/

		$this->add('name', 'text', [
			'label' => 'Nom de la séquence',
			'rules' => ['required', 'min:3']
		]);
		$this->add('content', 'textarea', [
			'label' => 'Textarea',
		]);
		$this->add('color', 'color', [
			'label' => 'Couleur',
			'default' => '#ffffff'
		]);
		$this->add('duration', 'number', [
			'label' => 'Durée (ms)',
			'rules' => ['required', 'numeric', 'min:100']
		]);
		$this->add('is_ok', 'checkbox', [
			'label' => 'Checkbox',
			
		]);

		$this->add('effect', 'select', [
			'label' => 'Effet',
			'options' => ['fade' => 'Fade', 'blink' => 'Blink'],
			'empty' => '',
			'rules' => ['required']
		]);

		$this->add('gender', 'radio', [
			'label' => 'Genre',
			'options' => [
				'male' => 'Homme',
				'female' => 'Femme',
				'other' => 'Autre'
			],
			//'value' => 'male', // Valeur par défaut
			'rules' => ['required']
		]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
