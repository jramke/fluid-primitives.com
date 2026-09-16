<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use Jramke\FluidPrimitives\Utility\Typed;
use Symfony\Component\Process\Process;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;

/**
 * Runs the full benchmark matrix (components x variants x instance-counts, plus the
 * cold/warm-disk/hot cache-state measurement per component/variant) and returns plain result
 * data - kept IO-free (no SymfonyStyle/OutputInterface) so BenchmarkRunCommand only has to
 * translate this data into console output.
 */
final readonly class BenchmarkOrchestrator
{
    public function __construct(
        private BenchmarkRunner $runner,
        private CacheManager $cacheManager,
    ) {}

    /**
     * @param list<string> $components
     * @param list<string> $variants
     * @param list<int> $instanceCounts
     * @return array{
     *     scaling: array<string, list<BenchmarkResult>>,
     *     cacheStates: list<CacheStateResult>,
     *     warmDiskFailures: list<array{component: string, variant: string}>,
     * }
     */
    public function run(array $components, array $variants, array $instanceCounts, BenchmarkRunOptions $options): array
    {
        $scaling = [];
        $allCacheStateResults = [];
        $warmDiskFailures = [];

        foreach ($components as $component) {
            $scalingResults = [];

            foreach ($variants as $variant) {
                if ($options->cacheStates !== []) {
                    $measured = $this->measureCacheStates($component, $variant, $options);
                    array_push($allCacheStateResults, ...$measured['results']);
                    array_push($warmDiskFailures, ...$measured['warmDiskFailures']);
                }

                foreach ($instanceCounts as $count) {
                    RegistrySnapshot::reset();
                    $scalingResults[] = $this->runner->runScaling($component, $variant, $count, $options);
                }
            }

            $scaling[$component] = $scalingResults;
        }

        return ['scaling' => $scaling, 'cacheStates' => $allCacheStateResults, 'warmDiskFailures' => $warmDiskFailures];
    }

    /**
     * @return array{results: list<CacheStateResult>, warmDiskFailures: list<array{component: string, variant: string}>}
     */
    private function measureCacheStates(string $component, string $variant, BenchmarkRunOptions $options): array
    {
        $cacheStates = $options->cacheStates;
        $request = $options->request;
        $results = [];
        $warmDiskFailures = [];
        $primed = false;

        if (in_array('cold', $cacheStates, strict: true)) {
            $this->cacheManager->getCache('fluid_template')->flush();
            RegistrySnapshot::reset();
            $cold = $this->runner->renderOnce($component, $variant, 1, $request);
            $results[] = new CacheStateResult($component, $variant, 'cold', $cold->elapsedMs);
            $primed = true;
        }

        // Both "warm-disk" (the disk cache must already have a compiled file to load) and "hot"
        // (the class must already be loaded in this process) assume the template was rendered at
        // least once before - without "cold" in the same run, that has to happen here instead, or
        // this first render would silently BE the cold compile while getting reported as
        // "warm-disk"/"hot".
        if (
            !$primed &&
            (in_array('warm-disk', $cacheStates, strict: true) || in_array('hot', $cacheStates, strict: true))
        ) {
            RegistrySnapshot::reset();
            $this->runner->renderOnce($component, $variant, 1, $request);
        }

        if (in_array('warm-disk', $cacheStates, strict: true)) {
            $warmDiskMs = $this->measureWarmDiskInSubprocess($component, $variant);
            if ($warmDiskMs !== null) {
                $results[] = new CacheStateResult($component, $variant, 'warm-disk', $warmDiskMs);
            }
            if ($warmDiskMs === null) {
                $warmDiskFailures[] = ['component' => $component, 'variant' => $variant];
            }
        }

        if (in_array('hot', $cacheStates, strict: true)) {
            RegistrySnapshot::reset();
            $hot = $this->runner->renderOnce($component, $variant, 1, $request);
            $results[] = new CacheStateResult($component, $variant, 'hot', $hot->elapsedMs);
        }

        return ['results' => $results, 'warmDiskFailures' => $warmDiskFailures];
    }

    /**
     * class_exists() state can't be reset from within a running process, so "warm-disk" (compiled
     * file already on disk, but not yet loaded in THIS process) can only be observed in a fresh
     * one - hence spawning `benchmark:run --worker` rather than rendering in-process.
     */
    private function measureWarmDiskInSubprocess(string $component, string $variant): ?float
    {
        $binary = Environment::getProjectPath() . '/vendor/bin/typo3';
        $process = new Process([
            PHP_BINARY,
            $binary,
            'benchmark:run',
            '--worker',
            '--component=' . $component,
            '--variant=' . $variant,
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        // @mago-expect analysis:mixed-assignment
        $decoded = json_decode(trim($process->getOutput()), associative: true);
        if (!is_array($decoded)) {
            return null;
        }

        return Typed::floatOrNull($decoded['elapsedMs'] ?? null);
    }
}
