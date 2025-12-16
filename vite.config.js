import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig(({ mode }) => ({
	plugins: [
		vue({
			customElement: true,
		})
	],
	resolve: {
		alias: {
			vue: 'vue/dist/vue.esm-bundler.js',
		},
	},
	publicDir: false,
	build: {
		outDir: 'public/dist',
		emptyOutDir: false,
		minify: mode === 'production',
		sourcemap: mode === 'development',
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
	},
	// Active les devtools Vue en dev
	define: {
		__VUE_PROD_DEVTOOLS__: mode === 'development',
		__VUE_OPTIONS_API__: true,
		__VUE_PROD_HYDRATION_MISMATCH_DETAILS__: mode === 'development'
	}
}))