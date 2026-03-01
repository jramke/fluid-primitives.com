<?php

declare(strict_types=1);

return [
    'frontend' => [
        'fluid-primitives/registry' => [
            'target' => \FluidPrimitives\Docs\Middleware\RegistryMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
