<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

final readonly class SingleRenderResult
{
    public function __construct(
        public float $elapsedMs,
        public int $memoryDeltaBytes,
        public string $html,
    ) {}

    public function htmlLength(): int
    {
        return strlen($this->html);
    }
}
