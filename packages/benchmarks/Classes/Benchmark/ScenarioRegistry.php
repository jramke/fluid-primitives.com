<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

/**
 * The fixed benchmark matrix: components x variants, plus the default instance-counts swept
 * for the scaling table. Component/variant keys double as folder and file name fragments -
 * `accordion` renders `Scenarios/{Variant}/Accordion.fluid.html`.
 */
final class ScenarioRegistry
{
    /** @return list<string> */
    public function components(): array
    {
        return ['accordion', 'checkbox', 'input', 'select'];
    }

    /** @return list<string> */
    public function variants(): array
    {
        return ['vanilla', 'direct', 'wrapped'];
    }

    /** @return list<int> */
    public function defaultInstanceCounts(): array
    {
        return [1, 10, 50, 200];
    }

    public function isValidComponent(string $component): bool
    {
        return in_array($component, $this->components(), strict: true);
    }

    public function isValidVariant(string $variant): bool
    {
        return in_array($variant, $this->variants(), strict: true);
    }

    /**
     * The Fluid template name to `render()`, relative to that variant's template root path.
     */
    public function templateName(string $component): string
    {
        return ucfirst($component);
    }

    /**
     * The folder under Resources/Private/Scenarios/ for a given variant.
     */
    public function variantFolder(string $variant): string
    {
        return ucfirst($variant);
    }
}
