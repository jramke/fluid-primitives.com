<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use Jramke\FluidPrimitives\Utility\Typed;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ArrayFillViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('count', 'int', 'Number of items in the array', true);
        $this->registerArgument('fill', 'mixed', 'Optional value to fill array with', false, null);
    }

    public function render(): array
    {
        $count = max(0, Typed::int($this->arguments['count']));
        // 'fill' is registered as a 'mixed' argument by design - callers fill the array with
        // whatever value they need, so there is no more specific type to narrow to here.
        // @mago-expect analysis:mixed-assignment
        $fill = $this->arguments['fill'];

        return array_fill(0, $count, $fill);
    }
}
