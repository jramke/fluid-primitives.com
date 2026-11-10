<?php

declare(strict_types=1);

use FluidPrimitives\Benchmarks\Component\BenchComponentCollection;
use FluidPrimitives\Benchmarks\Component\VanillaComponentCollection;

return [
    // Consumer-wrapper components (useProps/spreadProps around primitives:*), the "wrapped" variant.
    'bench' => [BenchComponentCollection::class],
    // Genuine Fluid components (f:argument/f:slot), no fluid-primitives involved - the "vanilla" variant.
    'vanilla' => [VanillaComponentCollection::class],
    // Registered independently of packages/docs so the Scenario templates can use <vite:asset>
    // regardless of whether docs is loaded.
    'vite' => ['Praetorius\\ViteAssetCollector\\ViewHelpers'],
];
