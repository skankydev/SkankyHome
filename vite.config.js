import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'


export default defineConfig({
	plugins: [vue()],
	publicDir: false,
	build: {
		outDir: 'public/dist',
		emptyOutDir: false,
		rollupOptions: {
			input: {
				app: './src_front/js/app.js',
				styles: './src_front/scss/main.scss'
			},
			output: {
				entryFileNames: '[name].js',
				assetFileNames: '[name].[ext]'
			}
		}
	}
})