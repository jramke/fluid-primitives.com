<?php

declare(strict_types=1);

use FluidPrimitives\Demos\Middleware\SearchSuggestions;

return [
    'frontend' => [
        'fluid-primitives/demos/search-suggestions' => [
            'target' => SearchSuggestions::class,
            'after' => ['typo3/cms-frontend/site'],
            'before' => ['typo3/cms-frontend/page-resolver'],
        ],
    ],
];
