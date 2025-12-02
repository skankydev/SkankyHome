"use strict";
import { createApp } from 'vue'
import LedEditor from './vue/LedEditor.vue'
import ColorPiker from './vue/ColorPiker.vue'



document.addEventListener('DOMContentLoaded', () => {


	
	/*const formInputs = document.querySelectorAll('.form-input, input, textarea');

	formInputs.forEach(input => {
		const formGroup = input.closest('.form-group');
		if (!formGroup) return;

			// Focus
			input.addEventListener('focus', () => {
			formGroup.classList.add('field-focus');
		});

		// Blur
		input.addEventListener('blur', () => {
			formGroup.classList.remove('field-focus');

			// Ajouter completed si rempli
			if (input.value.trim() !== '') {
				formGroup.classList.add('field-completed');
			} else {
				formGroup.classList.remove('field-completed');
			}
		});

		// Check initial state
		if (input.value.trim() !== '') {
			formGroup.classList.add('field-completed');
		}
	});*/

	let target = document.getElementById('MyLedEditor');
	if (target) {
		var editor = createApp({});
		editor.component('led-editor', LedEditor);
		editor.component('color-piker', ColorPiker);
		editor.mount('#MyLedEditor');
		
	}
})

