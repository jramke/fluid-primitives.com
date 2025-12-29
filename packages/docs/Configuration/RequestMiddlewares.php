<?php

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
