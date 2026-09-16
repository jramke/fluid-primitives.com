<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\ComponentNameUtility;
use Jramke\FluidPrimitives\Utility\Typed;

class ReferenceButtonsContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;

    public function getNameLowerCaseDashed(): string
    {
        return ComponentNameUtility::camelCaseToLowerCaseDashed(Typed::string($this->get('name')));
    }
}
