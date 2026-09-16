<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use Psr\Http\Message\ServerRequestInterface;

final readonly class BenchmarkRunner
{
    public function __construct(
        private BenchmarkViewFactory $viewFactory,
        private ScenarioRegistry $scenarioRegistry,
    ) {}

    /**
     * Renders one scenario exactly once and times it. This is the one primitive every other
     * measurement (scaling table, cache states) is built from.
     */
    public function renderOnce(
        string $component,
        string $variant,
        int $count,
        ServerRequestInterface $request,
    ): SingleRenderResult {
        $view = $this->viewFactory->createView($this->scenarioRegistry->variantFolder($variant), $request);
        $view->assignMultiple(ScenarioVariables::forCount($count));

        $memoryBefore = memory_get_usage(true);
        $start = hrtime(true);
        $html = $view->render($this->scenarioRegistry->templateName($component));
        $elapsedMs = (hrtime(true) - $start) / 1_000_000;

        return new SingleRenderResult($elapsedMs, memory_get_usage(true) - $memoryBefore, $html);
    }

    /**
     * The instance-count scaling measurement: always "hot" (see CacheStateResult) - warmup
     * iterations are discarded, then `$iterations` measured renders feed the reported stats.
     * HydrationRegistry/PortalRegistry are reset before every single iteration (warmup included),
     * otherwise later iterations would accumulate on top of earlier ones (see RegistrySnapshot).
     */
    public function runScaling(
        string $component,
        string $variant,
        int $count,
        BenchmarkRunOptions $options,
    ): BenchmarkResult {
        for ($i = 0; $i < $options->warmup; $i++) {
            RegistrySnapshot::reset();
            $this->renderOnce($component, $variant, $count, $options->request);
        }

        $samples = [];
        $memoryDeltas = [];
        for ($i = 0; $i < $options->iterations; $i++) {
            RegistrySnapshot::reset();
            $result = $this->renderOnce($component, $variant, $count, $options->request);
            $samples[] = $result->elapsedMs;
            $memoryDeltas[] = $result->memoryDeltaBytes;
        }

        $stats = Stats::compute($samples);

        return new BenchmarkResult(
            component: $component,
            variant: $variant,
            count: $count,
            iterations: $options->iterations,
            minMs: $stats['min'],
            maxMs: $stats['max'],
            meanMs: $stats['mean'],
            medianMs: $stats['median'],
            p95Ms: $stats['p95'],
            memoryDeltaBytes: (int)round(array_sum($memoryDeltas) / max(count($memoryDeltas), 1)),
        );
    }
}
