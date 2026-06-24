"use strict";
import { createApp } from 'vue'
import LedEditor from './Component/LedEditor.vue'
import ColorPicker from './Component/ColorPicker.vue'
import IconPicker from './Component/IconPicker.vue'
import LiveMode from './Led/LiveMode.vue'
import ScenarioMaker from './Led/ScenarioMaker.vue'
import MqttMonitor from './Led/MqttMonitor.vue'
import EffectPreview from './Led/EffectPreview.vue'
import PersonaChat from './Persona/PersonaChat.vue'
import ConversationChat from './Persona/ConversationChat.vue'

window.remove = function(element) {
	if (element && element.parentNode) {
		element.parentNode.removeChild(element);
	}
};

// Injection automatique du token CSRF sur toutes les requêtes fetch same-origin
// non-GET (forms AJAX, ScenarioMaker, tâches…). Le token vient du <meta> du layout.
;(() => {
	const meta = document.querySelector('meta[name="csrf-token"]');
	if (!meta) return;
	const token = meta.getAttribute('content');
	const nativeFetch = window.fetch.bind(window);

	window.fetch = function (resource, options = {}) {
		const method = (options.method || 'GET').toUpperCase();
		const target = resource instanceof Request ? resource.url : resource;
		const sameOrigin = new URL(target, window.location.origin).origin === window.location.origin;

		if (sameOrigin && method !== 'GET' && method !== 'HEAD') {
			options.headers = { ...(options.headers || {}), 'X-CSRF-Token': token };
		}
		return nativeFetch(resource, options);
	};
})();

// Liens qui déclenchent un POST (ex. delete) : <a data-method="post" data-confirm="…">.
// Au clic, on construit et soumet un <form method=post> avec le token CSRF.
// Capture = true pour passer AVANT le handler de ligne (.clickable-row).
document.addEventListener('click', (e) => {
	const link = e.target.closest('a[data-method]');
	if (!link) return;
	if ((link.dataset.method || '').toUpperCase() !== 'POST') return;

	e.preventDefault();
	e.stopPropagation();

	if (link.dataset.confirm && !confirm(link.dataset.confirm)) return;

	const form = document.createElement('form');
	form.method = 'POST';
	form.action = link.href;

	const meta = document.querySelector('meta[name="csrf-token"]');
	if (meta) {
		const input = document.createElement('input');
		input.type = 'hidden';
		input.name = '_token';
		input.value = meta.getAttribute('content');
		form.appendChild(input);
	}

	document.body.appendChild(form);
	form.submit();
}, true);


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

	target = document.getElementById('MqttMonitor');
	if (target) {
		var monitor = createApp({});
		monitor.component('mqtt-monitor', MqttMonitor);
		monitor.mount('#MqttMonitor');
	}

	target = document.getElementById('EffectPreview');
	if (target) {
		var preview = createApp({});
		preview.component('effect-preview', EffectPreview);
		preview.mount('#EffectPreview');
	}

	target = document.getElementById('PersonaChat');
	if (target) {
		var personaChat = createApp({});
		personaChat.component('persona-chat', PersonaChat);
		personaChat.mount('#PersonaChat');
	}

	target = document.getElementById('ConversationChat');
	if (target) {
		var conversationChat = createApp({});
		conversationChat.component('conversation-chat', ConversationChat);
		conversationChat.mount('#ConversationChat');
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