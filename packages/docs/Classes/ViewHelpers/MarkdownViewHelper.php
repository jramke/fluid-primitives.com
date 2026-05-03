<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use FluidPrimitives\Docs\Utility\DocsUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class MarkdownViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'The text to output', false, null, false);
    }

    public function render(): string
    {
        return DocsUtility::simpleMarkdownToHtml((string)($this->renderChildren() ?? ''));
    }

    public function getContentArgumentName(): string
    {
        return 'value';
    }
}
