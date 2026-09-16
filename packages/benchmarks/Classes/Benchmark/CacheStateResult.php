<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

/**
 * One cold/warm-disk/hot measurement for a (component, variant) template. Unlike BenchmarkResult
 * this is a single-sample measurement, not a distribution - each cache state can only honestly be
 * observed once per process (see BenchmarkRunCommand for why).
 */
final readonly class CacheStateResult
{
    public function __construct(
        public string $component,
        public string $variant,
        public string $cacheState,
        public float $elapsedMs,
    ) {}
}
