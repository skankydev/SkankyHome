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
})

