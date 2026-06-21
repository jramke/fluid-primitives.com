import type { StorybookConfig } from '@andersundsehr/storybook-typo3';
import { resolve } from 'node:path';
import { loadEnv, mergeConfig, type InlineConfig } from 'vite';

const rootStorybookEnvs = loadEnv(
    process.env.NODE_ENV || 'production',
    resolve(__dirname, '../'),
    'STORYBOOK_'
);

const config: StorybookConfig = {
    framework: '@andersundsehr/storybook-typo3',

    stories: ['../packages/docs/Resources/Private/Components/**/*.@(stories.@(js|jsx|ts|tsx))'],

    core: {
        disableTelemetry: true,
    },

    viteFinal: async config => {
        return mergeConfig(config, {
            server: {
                allowedHosts: true,
            },
        } satisfies Partial<InlineConfig>);
    },

    env: (envs, options) => {
        const isDev = envs?.NODE_ENV === 'development';

        return {
            STORYBOOK_TYPO3_ENDPOINT:
                process.env.STORYBOOK_TYPO3_ENDPOINT ?? process.env.DDEV_PRIMARY_URL ?? '',
            STORYBOOK_TYPO3_WATCH_ONLY_STORIES: '0',
            ...(isDev ? rootStorybookEnvs : {}),
            ...envs,
        };
    },
};
export default config;
