import type { StorybookConfig } from '@andersundsehr/storybook-typo3';
import { InlineConfig, mergeConfig } from 'vite';

const config: StorybookConfig = {
	framework: '@andersundsehr/storybook-typo3', // required

	stories: ['../packages/docs/Resources/Private/Components/**/*.stories.@(js|jsx|ts|tsx)'],

	core: {
		disableTelemetry: true,
	},

	viteFinal: async (config, option) => {
		return mergeConfig(config, {
			// server: {
			// 	allowedHosts: ['.ddev.site'],
			// 	hmr: {
			// 		clientPort: 8080,
			// 		protocol: 'wss',
			// 	},
			// },
			build: {
				target: ['es2022', 'edge89', 'firefox89', 'chrome89', 'safari15'],
			},
		} satisfies Partial<InlineConfig>);
	},

	env: envs => {
		return {
			// STORYBOOK_TYPO3_ENDPOINT: 'http://localhost/_storybook/',
			STORYBOOK_TYPO3_ENDPOINT: process.env.DDEV_PRIMARY_URL + '/_storybook/',
			// STORYBOOK_TYPO3_ENDPOINT: 'https://slimstart-test.dev' + '/_storybook/',
			STORYBOOK_TYPO3_WATCH_ONLY_STORIES: '0', // set to '1' If you already use vite in your TYPO3 with HMR
			// do not set your api key here! https://www.deployhq.com/blog/protecting-your-api-keys-a-quick-guide
			...envs, // envs given to storybook have precedence
		};
	},
};
export default config;
