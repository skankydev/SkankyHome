"use strict";
import { createApp } from 'vue'
import LedEditor from './Component/LedEditor.vue'
import ColorPicker from './Component/ColorPicker.vue'
import IconPicker from './Component/IconPicker.vue'
import LiveMode from './Led/LiveMode.vue'
import ScenarioMaker from './Led/ScenarioMaker.vue'

window.remove = function(element) {
	if (element && element.parentNode) {
		element.parentNode.removeChild(element);
	}
}


document.addEventListener('DOMContentLoaded', () => {
	
	document.querySelectorAll('.clickable-row').forEach(row => {
		row.addEventListener('click', function() {
			console.log(this.dataset.url);
			window.location.href = this.dataset.url;
		});
	});

	let target = document.getElementById('MyLedEditor');
	if (target) {
		var editor = createApp({});
		editor.component('led-editor', LedEditor);
		editor.component('color-picker', ColorPicker);
		editor.mount('#MyLedEditor');
	}

	target = document.getElementById('AppForm');
	if (target) {
		var editor = createApp({});
		editor.component('icon-picker', IconPicker);
		editor.mount('#AppForm');
	}

	target = document.getElementById('LiveMode');
	if (target) {
		var editor = createApp({});
		editor.component('live-mode', LiveMode);
		editor.mount('#LiveMode');
	}

	target = document.getElementById('ScenarioMaker');
	if (target) {
		var scenario = createApp({});
		scenario.component('scenario-maker', ScenarioMaker);
		scenario.mount('#ScenarioMaker');
	}

	initBurger();

	
	
})

function initBurger(){
	const burgerWrapper = document.getElementById('BurgerWrapper');
	const burgerMenu = burgerWrapper?.querySelector('.burger-menu');
	const burgerContent = burgerWrapper?.querySelector('.burger-content');
	const logoClose = burgerWrapper?.querySelector('.logo-skankyhome');
	
	if (!burgerWrapper || !burgerMenu) return;
	
	// Créer l'overlay
	const overlay = document.createElement('div');
	overlay.className = 'burger-overlay';
	burgerWrapper.parentNode.insertBefore(overlay, burgerWrapper.nextSibling);
	
	// Fonction pour ouvrir
	window.openBurger = function() {
		burgerWrapper.classList.add('active');
		document.body.style.overflow = 'hidden'; // Empêcher le scroll
	}
	
	// Fonction pour fermer
	window.closeBurger = function() {
		burgerWrapper.classList.remove('active');
		document.body.style.overflow = ''; // Rétablir le scroll
	}
	
	// Toggle au clic sur le burger
	burgerMenu.addEventListener('click', () => {
		if (burgerWrapper.classList.contains('active')) {
			closeBurger();
		} else {
			openBurger();
		}
	});
	
	// Fermer au clic sur le logo
	if (logoClose) {
		logoClose.addEventListener('click', closeBurger);
	}
	
	// Fermer au clic sur l'overlay
	overlay.addEventListener('click', closeBurger);
}