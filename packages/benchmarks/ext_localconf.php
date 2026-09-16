<?php

declare(strict_types=1);

use FluidPrimitives\Benchmarks\Component\BenchComponentCollection;
use FluidPrimitives\Benchmarks\Component\VanillaComponentCollection;

// @mago-expect analysis:unused-statement
defined('TYPO3') || die('Access denied.');

// Consumer-wrapper components (useProps/spreadProps around primitives:*), the "wrapped" variant.
// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['bench'] = [
    BenchComponentCollection::class,
];

// Genuine Fluid components (f:argument/f:slot), no fluid-primitives involved - the "vanilla" variant.
// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
// @mago-expect analysis:mixed-array-assignment
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vanilla'] = [
    VanillaComponentCollection::class,
];

// Registered independently of packages/docs (which also registers this) so the benchmarks
// package's own Scenario templates can use <vite:asset> regardless of whether docs is loaded.
// @mago-expect lint:no-global,no-isset
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'])) {
    // @mago-expect lint:no-global
    // @mago-expect analysis:mixed-array-assignment
    // @mago-expect analysis:mixed-array-assignment
    // @mago-expect analysis:mixed-array-assignment
    // @mago-expect analysis:mixed-array-assignment
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'] = ['Praetorius\\ViteAssetCollector\\ViewHelpers'];
}
