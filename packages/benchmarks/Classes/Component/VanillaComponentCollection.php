<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Component;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\Component\AbstractComponentCollection;
use TYPO3Fluid\Fluid\View\TemplatePaths;

/**
 * The "vanilla" variant: genuine Fluid components (`f:argument`/`f:slot`) built directly on
 * TYPO3Fluid's own `TYPO3Fluid\Fluid\Core\Component\AbstractComponentCollection` - no
 * fluid-primitives involved at all. This is what isolates "cost of Fluid's own native component
 * system" from "cost of what fluid-primitives adds on top of it" (DI-resolved contexts,
 * hydration collection, spreadProps/useProps, ...), which a flat, non-componentized template
 * couldn't do - it would skip Fluid's own component-parsing/rendering pipeline entirely rather
 * than measuring it.
 */
final class VanillaComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('benchmarks', 'Resources/Private/Components/Vanilla'),
        ]);
        return $templatePaths;
    }

    /**
     * Flat `{Name}/{Part}.fluid.html` layout (matching this package's `primitives`/`bench`
     * collections) instead of the core default's `{Name}/{Part}/{Part}.fluid.html`.
     */
    public function resolveTemplateName(string $viewHelperName): string
    {
        $fragments = array_map(ucfirst(...), explode('.', $viewHelperName));
        return implode('/', $fragments);
    }
}
