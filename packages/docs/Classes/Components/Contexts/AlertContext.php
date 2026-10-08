<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

class AlertContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;
}
