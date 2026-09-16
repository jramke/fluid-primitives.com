<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use Symfony\Component\Console\Style\SymfonyStyle;

final class ResultFormatter
{
    /**
     * @param list<BenchmarkResult> $results
     */
    public function printScalingTable(SymfonyStyle $io, string $component, array $results): void
    {
        $io->section("Instance-count scaling: {$component}");

        $byVariant = [];
        foreach ($results as $result) {
            $byVariant[$result->variant][] = $result;
        }

        $vanillaByCount = $this->indexByCount($byVariant['vanilla'] ?? []);
        $directByCount = $this->indexByCount($byVariant['direct'] ?? []);

        $rows = [];
        foreach ($results as $result) {
            $vanilla = $vanillaByCount[$result->count] ?? null;
            $direct = $directByCount[$result->count] ?? null;

            $rows[] = [
                $result->variant,
                $result->count,
                number_format($result->meanMs, decimals: 3),
                number_format($result->medianMs, decimals: 3),
                number_format($result->p95Ms, decimals: 3),
                number_format($result->minMs, decimals: 3),
                number_format($result->maxMs, decimals: 3),
                number_format($result->memoryDeltaBytes / 1024, decimals: 1),
                $vanilla instanceof BenchmarkResult ? $this->overhead($result, $vanilla) : '-',
                $direct instanceof BenchmarkResult ? $this->overhead($result, $direct) : '-',
            ];
        }

        $io->table([
            'Variant',
            'Count',
            'Mean ms',
            'Median ms',
            'p95 ms',
            'Min ms',
            'Max ms',
            'Memory KB',
            '% vs vanilla',
            '% vs direct',
        ], $rows);
    }

    /**
     * @param list<CacheStateResult> $results
     */
    public function printCacheStateTable(SymfonyStyle $io, array $results): void
    {
        $io->section('Template cache state: cold vs. warm-disk vs. hot');

        $rows = array_map(static fn(CacheStateResult $result): array => [
            $result->component,
            $result->variant,
            $result->cacheState,
            number_format($result->elapsedMs, decimals: 3),
        ], $results);

        $io->table(['Component', 'Variant', 'Cache state', 'Elapsed ms'], $rows);
    }

    /**
     * @param list<BenchmarkResult> $scalingResults
     * @param list<CacheStateResult> $cacheStateResults
     */
    public function toJson(array $scalingResults, array $cacheStateResults): string
    {
        $payload = [
            'generatedAt' => date('c'),
            'scaling' => array_map(static fn(BenchmarkResult $r): array => [
                'component' => $r->component,
                'variant' => $r->variant,
                'count' => $r->count,
                'iterations' => $r->iterations,
                'minMs' => $r->minMs,
                'maxMs' => $r->maxMs,
                'meanMs' => $r->meanMs,
                'medianMs' => $r->medianMs,
                'p95Ms' => $r->p95Ms,
                'memoryDeltaBytes' => $r->memoryDeltaBytes,
            ], $scalingResults),
            'cacheStates' => array_map(static fn(CacheStateResult $r): array => [
                'component' => $r->component,
                'variant' => $r->variant,
                'cacheState' => $r->cacheState,
                'elapsedMs' => $r->elapsedMs,
            ], $cacheStateResults),
        ];

        return json_encode($payload, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    /**
     * @param list<BenchmarkResult> $results
     * @return array<int, BenchmarkResult>
     */
    private function indexByCount(array $results): array
    {
        $indexed = [];
        foreach ($results as $result) {
            $indexed[$result->count] = $result;
        }
        return $indexed;
    }

    private function overhead(BenchmarkResult $result, BenchmarkResult $baseline): string
    {
        if ($baseline->meanMs <= 0.0) {
            return '-';
        }

        $percent = (($result->meanMs - $baseline->meanMs) / $baseline->meanMs) * 100;
        return sprintf('%+.1f%%', $percent);
    }
}
