import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import typo3, { getDefaultIgnoreList } from 'vite-plugin-typo3';

export default defineConfig({
	plugins: [typo3(), tailwindcss()],
	server: {
		watch: {
			ignored: [...getDefaultIgnoreList(), '**/*.{html,php,typoscript,yaml,tsconfig,ts,js}'],
		},
	},
});
