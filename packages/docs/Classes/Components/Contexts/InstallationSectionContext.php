<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\ComponentUtility;

class InstallationSectionContext extends AbstractComponentContext
{
    public function getNameLowerCaseDashed(): string
    {
        return ComponentUtility::camelCaseToLowerCaseDashed($this->get('name') ?? '');
    }
}
