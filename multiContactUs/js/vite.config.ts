import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import { viteStaticCopy } from 'vite-plugin-static-copy'
import path from 'node:path'

export default defineConfig({
	plugins: [
		vue(),
		tailwindcss(),
		viteStaticCopy({
			targets: [
				{
					src: 'src/assets/gfx/*',
					dest: 'gfx' // this will be inside your outDir
				}
			]
		})
	],
	build: {
		outDir: '/var/www/wordpress/wp-content/plugins/contact-us/assets',
		emptyOutDir: false,
		sourcemap: true,
		minify:false,
		cssCodeSplit: false,
		lib: {
			entry: path.resolve(__dirname, 'main.js'), // your main JS entry (createApp code)
			name: 'ContactUsApp', // global variable name
			formats: ['iife']     // Immediately Invoked Function Expression (classic script)
		},
		rollupOptions: {
			output: {
				assetFileNames: '[name].[ext]',
				chunkFileNames: '[name].js',
				entryFileNames: '[name].js'
			},
			input: path.resolve(__dirname, 'index.html')
		}
	},
	resolve: {
		alias: {
			'@': path.resolve(__dirname, './src'),
		},
	},
		define: {
			'process.env.NODE_ENV': JSON.stringify('production')
		},
	base: '', // important for WordPress
})

