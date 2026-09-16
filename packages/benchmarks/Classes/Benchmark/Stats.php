<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

/**
 * Descriptive statistics over a set of millisecond samples. Kept isolated from BenchmarkRunner
 * since it's the one piece of non-obvious, easy-to-get-wrong math in this package.
 */
final class Stats
{
    /**
     * @param list<float> $samples
     * @return array{min: float, max: float, mean: float, median: float, p95: float}
     */
    public static function compute(array $samples): array
    {
        $count = count($samples);
        if ($count === 0) {
            return ['min' => 0.0, 'max' => 0.0, 'mean' => 0.0, 'median' => 0.0, 'p95' => 0.0];
        }

        sort($samples);

        return [
            'min' => $samples[0],
            'max' => $samples[$count - 1],
            'mean' => array_sum($samples) / $count,
            'median' => self::median($samples),
            'p95' => self::percentile($samples, 0.95),
        ];
    }

    /**
     * @param list<float> $sorted Already sorted ascending.
     */
    private static function median(array $sorted): float
    {
        $count = count($sorted);
        $middle = intdiv($count, num2: 2);

        if (($count % 2) === 0) {
            return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
        }

        return $sorted[$middle];
    }

    /**
     * Nearest-rank method.
     *
     * @param list<float> $sorted Already sorted ascending.
     */
    private static function percentile(array $sorted, float $percentile): float
    {
        $count = count($sorted);
        $rank = (int)ceil($percentile * $count);
        $index = max(0, min($count - 1, $rank - 1));

        return $sorted[$index];
    }
}
