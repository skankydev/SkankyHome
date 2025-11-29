import { createApp } from 'vue'
import LedEditor from './vue/LedEditor'


document.addEventListener('DOMContentLoaded', () => {

	const target = document.getElementById('LedEditor')
	if (target) {
		const app = createApp({})
		app.component('led-editor', LedEditor)
		app.mount('#LedEditor')
	}

})
