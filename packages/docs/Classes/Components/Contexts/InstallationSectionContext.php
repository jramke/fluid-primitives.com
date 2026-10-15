<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\Typed;

class InstallationSectionContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;

    public function getNameLowerCamelCase(): string
    {
        return lcfirst(Typed::string($this->get('name')));
    }
}
