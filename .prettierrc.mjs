import { readFileSync } from 'node:fs';

// The package owns the Prettier rules so its standalone CI shares them with the monorepo.
export default JSON.parse(
    readFileSync(new URL('./packages/fluid-primitives/.prettierrc', import.meta.url), 'utf8')
);
