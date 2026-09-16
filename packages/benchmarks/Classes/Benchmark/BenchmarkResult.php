<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

/**
 * Instance-count scaling result for one (component, variant, count) cell - always measured
 * "hot" (the template's compiled class already loaded in this process), since cache state and
 * instance-count are orthogonal axes (see CacheStateResult for the former).
 */
final readonly class BenchmarkResult
{
    public function __construct(
        public string $component,
        public string $variant,
        public int $count,
        public int $iterations,
        public float $minMs,
        public float $maxMs,
        public float $meanMs,
        public float $medianMs,
        public float $p95Ms,
        public int $memoryDeltaBytes,
    ) {}
}
