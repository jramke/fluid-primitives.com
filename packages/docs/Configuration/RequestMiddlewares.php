<?php

declare(strict_types=1);

use FluidPrimitives\Docs\Middleware\RegistryMiddleware;

return [
    'frontend' => [
        'fluid-primitives/registry' => [
            'target' => RegistryMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
