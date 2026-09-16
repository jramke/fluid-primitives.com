<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Tests\Unit\Benchmark;

use FluidPrimitives\Benchmarks\Benchmark\Stats;
use FluidPrimitives\Benchmarks\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class StatsTest extends TestCase
{
    #[Test]
    public function computesMinMaxMeanFromUnsortedSamples(): void
    {
        $stats = Stats::compute([5.0, 1.0, 3.0, 2.0, 4.0]);

        $this->assertSame(1.0, $stats['min']);
        $this->assertSame(5.0, $stats['max']);
        $this->assertSame(3.0, $stats['mean']);
    }

    #[Test]
    public function averagesTheTwoMiddleValuesForAnEvenSampleCount(): void
    {
        $stats = Stats::compute([1.0, 2.0, 3.0, 4.0]);

        $this->assertSame(2.5, $stats['median']);
    }

    #[Test]
    public function picksTheMiddleValueForAnOddSampleCount(): void
    {
        $stats = Stats::compute([1.0, 2.0, 3.0]);

        $this->assertSame(2.0, $stats['median']);
    }

    #[Test]
    public function computesP95UsingNearestRank(): void
    {
        // 20 samples: nearest-rank p95 = ceil(0.95 * 20) = 19th value (1-indexed) => index 18 => 19.0
        $samples = array_map(static fn(int $n): float => (float)$n, range(1, end: 20));

        $stats = Stats::compute($samples);

        $this->assertSame(19.0, $stats['p95']);
    }

    #[Test]
    public function returnsZeroedStatsForNoSamples(): void
    {
        $stats = Stats::compute([]);

        $this->assertSame(['min' => 0.0, 'max' => 0.0, 'mean' => 0.0, 'median' => 0.0, 'p95' => 0.0], $stats);
    }
}
