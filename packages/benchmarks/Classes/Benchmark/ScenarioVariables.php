<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

/**
 * Builds the variables every Scenario template renders with. Fluid has no numeric-range
 * iterator, so `count` is always accompanied by a pre-built `indices` array (used by the
 * Accordion/Checkbox/Input scenarios to loop that many root instances) and an `items` array
 * (used by the Select scenario to build a collection of that many options) - which of the two
 * a given template actually uses depends on which axis that component benchmarks (see the
 * "wrapped" Scenario templates for details).
 */
final class ScenarioVariables
{
    /**
     * @return array{count: int, indices: list<int>, items: list<array{value: string, label: string}>}
     */
    public static function forCount(int $count): array
    {
        $indices = $count > 0 ? range(0, $count - 1) : [];

        return [
            'count' => $count,
            'indices' => $indices,
            'items' => array_map(static fn(int $i): array => [
                'value' => "item-{$i}",
                'label' => "Item {$i}",
            ], $indices),
        ];
    }
}
