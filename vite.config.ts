import tailwindcss from '@tailwindcss/vite';
import { defineConfig, type Plugin } from 'vite';
import typo3 from 'vite-plugin-typo3';

export default defineConfig({
    plugins: [typo3({ aliases: 'EXT' }), tailwindcss(), injectIgnoreFullReload()],
});

// Prevents all kind of full reloads triggered by vite, real hmr for css or tsx still works
function injectIgnoreFullReload(): Plugin {
    const ENTRY_FILE_REGEX = /\.entry\.[jt]sx?$/;

    // Throwing inside the 'vite:beforeFullReload' event handler will prevent a full reload from happening.
    // With the unhandledrejection we prevent the error from being logged in the console.
    const IGNORE_FULL_RELOAD_SNIPPET = `
        if (import.meta.hot) {
            const __ignoreFullReloadMarker = Symbol('ignored-full-reload');
            window.addEventListener('unhandledrejection', (event) => {
                if (event.reason === __ignoreFullReloadMarker) event.preventDefault();
            });
            import.meta.hot.on('vite:beforeFullReload', () => { throw __ignoreFullReloadMarker; });
        }
    `;

    return {
        name: 'inject-ignore-full-reload',
        apply: 'serve',
        transform(code, id) {
            const [filePath] = id.split('?');

            const isEntryFile = ENTRY_FILE_REGEX.test(filePath);
            if (!isEntryFile) return;

            return { code: IGNORE_FULL_RELOAD_SNIPPET + code };
        },
    };
}
