<?php 

namespace App\Form;

use SkankyDev\Form\FormBuilder;

class TestForm extends FormBuilder{

	public function build() : void {
		$this->add('name', 'text', [
			'label' => 'Nom de la séquence',
			'rules' => ['required', 'min:3']
		]);
		$this->add('color', 'color', [
			'label' => 'Couleur',
			'default' => '#ffffff'
		]);
		$this->add('duration', 'number', [
			'label' => 'Durée (ms)',
			'rules' => ['required', 'numeric', 'min:100']
		]);
		$this->add('effect', 'select', [
			'label' => 'Effet',
			'options' => ['fade' => 'Fade', 'blink' => 'Blink'],
			'rules' => ['required']
		]);
		$this->submit('Créer');
	}
}