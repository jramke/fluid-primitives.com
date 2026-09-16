<?php

declare(strict_types=1);

use FluidPrimitives\Benchmarks\Middleware\BenchmarkMiddleware;

return [
    'frontend' => [
        'fluid-primitives/benchmarks' => [
            'target' => BenchmarkMiddleware::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
