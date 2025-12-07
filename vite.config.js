import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'


export default defineConfig({
	plugins: [vue({
		customElement:true,
	})],
	resolve: {
		alias: {
			vue: 'vue/dist/vue.esm-bundler.js',
		},
	},
	publicDir: false,
	build: {
		outDir: 'public/dist',
		emptyOutDir: false,
		minify: true,
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