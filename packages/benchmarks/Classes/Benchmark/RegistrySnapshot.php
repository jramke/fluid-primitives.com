<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use Jramke\FluidPrimitives\Registry\HydrationRegistry;
use Jramke\FluidPrimitives\Registry\PortalRegistry;

/**
 * HydrationRegistry and PortalRegistry are process-lifetime singletons (a static `$instance`
 * cache with no request-scoped reconstruction). Left alone across repeated measured iterations,
 * their internal arrays grow unboundedly - every iteration's components get fresh ids, so old
 * entries are never overwritten - which both skews timing (later iterations doing more
 * accumulated JSON-encode/minify work than earlier ones) and directly invalidates the very
 * HydrationRegistry::add() rebuild-cost measurement this tool cares about. Both classes already
 * expose a public reset for exactly this (used by this project's own
 * Functional/FunctionalTestCase too), so no reflection trickery is needed here.
 */
final class RegistrySnapshot
{
    public static function reset(): void
    {
        HydrationRegistry::getInstance()->clear();
        PortalRegistry::getInstance()->clearAll();
    }
}
