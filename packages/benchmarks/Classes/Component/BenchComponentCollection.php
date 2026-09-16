<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Component;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

/**
 * Consumer-wrapper components for the "wrapped" benchmark variant - the same
 * useProps/spreadProps pattern packages/docs uses around primitives:*, kept in this
 * package's own `bench` namespace so it doesn't collide with docs' `ui` collection.
 */
final class BenchComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('benchmarks', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }
}
