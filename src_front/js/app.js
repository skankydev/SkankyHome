import { createApp } from 'vue'
import LedEditor from './vue/LedEditor.vue'



document.addEventListener('DOMContentLoaded', () => {

	let target = document.getElementById('MyLedEditor')
	console.log('Target trouvé ?', target);

	if (target) {
		let editor = createApp({});
		editor.component('led-editor', LedEditor);
		editor.mount('#MyLedEditor');
		console.log(editor);
	}
})

//createApp(LedEditor).mount('#LedEditor')
