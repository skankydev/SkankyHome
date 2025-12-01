"use strict";
import { createApp } from 'vue'
import LedEditor from './vue/LedEditor.vue'
import ColorPiker from './vue/ColorPiker.vue'



document.addEventListener('DOMContentLoaded', () => {

	let target = document.getElementById('MyLedEditor');
	

	if (target) {
		var editor = createApp({});
		editor.component('led-editor', LedEditor);
		editor.component('color-piker', ColorPiker);
		editor.mount('#MyLedEditor');
		
	}
})

