"use strict";
import { createApp } from 'vue'
import LedEditor from './Component/LedEditor.vue'
import ColorPiker from './Component/ColorPiker.vue'
import LiveMode from './Led/LiveMode.vue'

window.remove = function(element) {
	if (element && element.parentNode) {
		element.parentNode.removeChild(element);
	}
}


document.addEventListener('DOMContentLoaded', () => {

	let target = document.getElementById('MyLedEditor');
	if (target) {
		var editor = createApp({});
		editor.component('led-editor', LedEditor);
		editor.component('color-piker', ColorPiker);
		editor.mount('#MyLedEditor');
		
	}

	target = document.getElementById('LiveMode');
	if (target) {
		var editor = createApp({});
		editor.component('live-mode', LiveMode);
		editor.mount('#LiveMode');
		
	}
})

