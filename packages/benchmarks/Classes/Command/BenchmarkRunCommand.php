<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Command;

use FluidPrimitives\Benchmarks\Benchmark\BenchmarkOrchestrator;
use FluidPrimitives\Benchmarks\Benchmark\BenchmarkRunner;
use FluidPrimitives\Benchmarks\Benchmark\BenchmarkRunOptions;
use FluidPrimitives\Benchmarks\Benchmark\BenchmarkViewFactory;
use FluidPrimitives\Benchmarks\Benchmark\ResultFormatter;
use FluidPrimitives\Benchmarks\Benchmark\ScenarioRegistry;
use Jramke\FluidPrimitives\Utility\Typed;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Renders each (component, variant) scenario in this package's fixed benchmark matrix and
 * reports render-time/memory stats: a scaling table across instance-counts, and a
 * cold/warm-disk/hot template-cache-state table (see BenchmarkOrchestrator). Invoke as
 * `ddev typo3 benchmark:run [options]`.
 */
#[AsCommand(
    name: 'benchmark:run',
    description: 'Benchmark Fluid Primitives rendering across vanilla/direct/wrapped usage',
)]
final class BenchmarkRunCommand extends Command
{
    public function __construct(
        private readonly BenchmarkOrchestrator $orchestrator,
        private readonly BenchmarkRunner $runner,
        private readonly BenchmarkViewFactory $viewFactory,
        private readonly ScenarioRegistry $scenarioRegistry,
        private readonly ResultFormatter $formatter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('components', null, InputOption::VALUE_OPTIONAL, 'Comma-separated components (default: all)');
        $this->addOption(
            'variants',
            null,
            InputOption::VALUE_OPTIONAL,
            'Comma-separated variants: vanilla,direct,wrapped (default: all)',
        );
        $this->addOption(
            'instances',
            null,
            InputOption::VALUE_OPTIONAL,
            'Comma-separated instance counts (default: 1,10,50,200)',
        );
        $this->addOption('iterations', null, InputOption::VALUE_OPTIONAL, 'Measured iterations per scenario', '30');
        $this->addOption(
            'warmup',
            null,
            InputOption::VALUE_OPTIONAL,
            'Warmup iterations per scenario (discarded)',
            '5',
        );
        $this->addOption(
            'cache-states',
            null,
            InputOption::VALUE_OPTIONAL,
            'Comma-separated cold,warm-disk,hot (default: all three)',
        );
        $this->addOption('json', null, InputOption::VALUE_OPTIONAL, 'Write raw results as JSON to this path');
        $this->addOption(
            'worker',
            null,
            InputOption::VALUE_NONE,
            'Internal: render a single scenario once and print its timing as JSON. Used to measure the ' .
            '"warm-disk" cache state in an isolated process - class-loaded state can\'t be reset from ' .
            'within a running process, so that state can only be observed in a fresh one.',
        );
        $this->addOption('component', null, InputOption::VALUE_OPTIONAL, 'Internal: --worker mode component');
        $this->addOption('variant', null, InputOption::VALUE_OPTIONAL, 'Internal: --worker mode variant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('worker')) {
            return $this->executeWorker($input, $output);
        }

        $io = new SymfonyStyle($input, $output);

        $options = $this->resolveOptions($input, $io);
        if ($options === null) {
            return Command::FAILURE;
        }

        $runOptions = new BenchmarkRunOptions(
            iterations: $options['iterations'],
            warmup: $options['warmup'],
            cacheStates: $options['cacheStates'],
            request: $this->viewFactory->createFrontendRequest(),
        );
        $result = $this->orchestrator->run(
            $options['components'],
            $options['variants'],
            $options['instanceCounts'],
            $runOptions,
        );

        foreach ($result['scaling'] as $component => $scalingResults) {
            $this->formatter->printScalingTable($io, $component, $scalingResults);
        }

        foreach ($result['warmDiskFailures'] as $failure) {
            $io->warning(sprintf(
                'Skipped warm-disk measurement for %s/%s (subprocess failed).',
                $failure['component'],
                $failure['variant'],
            ));
        }

        if ($result['cacheStates'] !== []) {
            $this->formatter->printCacheStateTable($io, $result['cacheStates']);
        }

        $jsonPath = Typed::stringOrNull($input->getOption('json'));
        if ($jsonPath !== null && $jsonPath !== '') {
            $allScalingResults = array_merge(...array_values($result['scaling']));
            file_put_contents($jsonPath, $this->formatter->toJson($allScalingResults, $result['cacheStates']));
            $io->success(sprintf('Wrote raw results to %s', $jsonPath));
        }

        return Command::SUCCESS;
    }

    /**
     * @return array{
     *     components: list<string>,
     *     variants: list<string>,
     *     instanceCounts: list<int>,
     *     iterations: int,
     *     warmup: int,
     *     cacheStates: list<string>,
     * }|null
     */
    private function resolveOptions(InputInterface $input, SymfonyStyle $io): ?array
    {
        $components = $this->parseList($input->getOption('components')) ?? $this->scenarioRegistry->components();
        $variants = $this->parseList($input->getOption('variants')) ?? $this->scenarioRegistry->variants();
        $instanceCounts = array_map(
            intval(...),
            $this->parseList($input->getOption('instances')) ?? array_map(
                strval(...),
                $this->scenarioRegistry->defaultInstanceCounts(),
            ),
        );

        $unknownComponents = array_diff($components, $this->scenarioRegistry->components());
        if ($unknownComponents !== []) {
            $io->error(sprintf(
                'Unknown component(s): %s. Available: %s',
                implode(', ', $unknownComponents),
                implode(', ', $this->scenarioRegistry->components()),
            ));
            return null;
        }

        $unknownVariants = array_diff($variants, $this->scenarioRegistry->variants());
        if ($unknownVariants !== []) {
            $io->error(sprintf(
                'Unknown variant(s): %s. Available: %s',
                implode(', ', $unknownVariants),
                implode(', ', $this->scenarioRegistry->variants()),
            ));
            return null;
        }

        return [
            'components' => $components,
            'variants' => $variants,
            'instanceCounts' => $instanceCounts,
            'iterations' => max(1, (int)$input->getOption('iterations')),
            'warmup' => max(0, (int)$input->getOption('warmup')),
            'cacheStates' => $this->parseList($input->getOption('cache-states')) ?? ['cold', 'warm-disk', 'hot'],
        ];
    }

    private function executeWorker(InputInterface $input, OutputInterface $output): int
    {
        $component = Typed::stringOrNull($input->getOption('component'));
        $variant = Typed::stringOrNull($input->getOption('variant'));

        if (
            $component === null ||
            $variant === null ||
            !$this->scenarioRegistry->isValidComponent($component) ||
            !$this->scenarioRegistry->isValidVariant($variant)
        ) {
            $output->writeln((string)json_encode(['error' => 'invalid --component/--variant']));
            return Command::FAILURE;
        }

        $request = $this->viewFactory->createFrontendRequest();
        $result = $this->runner->renderOnce($component, $variant, 1, $request);

        $output->writeln(json_encode(['elapsedMs' => $result->elapsedMs], JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }

    /**
     * @return list<string>|null
     */
    private function parseList(mixed $value): ?array
    {
        $string = Typed::stringOrNull($value);
        if ($string === null || trim($string) === '') {
            return null;
        }

        $parts = array_map(trim(...), explode(',', $string));
        return array_values(array_filter($parts, static fn(string $part): bool => $part !== ''));
    }
}
