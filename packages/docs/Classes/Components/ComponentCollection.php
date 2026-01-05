<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use Jramke\FluidPrimitives\Traits\ComponentAssetAutoLoaderTrait;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    use ComponentAssetAutoLoaderTrait;

    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('docs', 'Resources/Private/Components/ui'),
            ExtensionManagementUtility::extPath('docs', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }

    public function getContextNamespaces(): array
    {
        return [
            'FluidPrimitives\Docs\Components\Contexts',
        ];
    }

    public function getComponentEntryExtensions(string $viewHelperName): array
    {
        return ['.entry.ts', '.entry.tsx'];
    }

    // public function loadComponentAsset(string $fileName, string $viewHelperName)
    // {
    //     krexx([$fileName, $viewHelperName]);
    // }
}
