<?php

declare(strict_types=1);

use FluidPrimitives\Docs\Middleware\DocsMarkdownModeMiddleware;
use FluidPrimitives\Docs\Middleware\LlmsTxtMiddleware;
use FluidPrimitives\Docs\Middleware\RegistryMiddleware;

return [
    'frontend' => [
        'fluid-primitives/registry' => [
            'target' => RegistryMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
        'fluid-primitives/llms-txt' => [
            'target' => LlmsTxtMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
        'fluid-primitives/docs-markdown-mode' => [
            'target' => DocsMarkdownModeMiddleware::class,
            // Unlike the other two, this one rewrites the URI and lets the request continue -
            // routing (which resolves the `{path}` route enhancer via ValidatedPathMapper) happens
            // in the `site` middleware, so the `.md` suffix has to be gone before that, not just
            // before page-resolver.
            'before' => [
                'typo3/cms-frontend/site',
            ],
        ],
    ],
];
