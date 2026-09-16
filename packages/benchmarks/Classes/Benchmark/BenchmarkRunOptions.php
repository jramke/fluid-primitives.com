<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use Psr\Http\Message\ServerRequestInterface;

final readonly class BenchmarkRunOptions
{
    /**
     * @param list<string> $cacheStates
     */
    public function __construct(
        public int $iterations,
        public int $warmup,
        public array $cacheStates,
        public ServerRequestInterface $request,
    ) {}
}
