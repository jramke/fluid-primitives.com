<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Escapes a value for use inside a Markdown table cell (see the `.md` request path's props/Zag tables) -
 * a literal `|` would otherwise split the cell, and a literal newline would break the row. Both show up
 * routinely in this data: TypeScript union types (`string | null`) and quoted state values
 * (`"open" | "closed"`) in prop descriptions and Zag metadata.
 */
final class EscapeMarkdownTableCellViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'The text to output', false, null, false);
    }

    public function render(): string
    {
        $value = (string)($this->renderChildren() ?? '');
        $value = str_replace(["\r\n", "\n", "\r"], replace: ' ', subject: $value);

        return str_replace('|', replace: '\\|', subject: $value);
    }

    public function getContentArgumentName(): string
    {
        return 'value';
    }
}
