<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\PaginationExamples;

use FluidPrimitives\Docs\Services\EventArchive;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

/**
 * Plays the controller of the link examples: it reads the page argument and assigns a `paginator`
 * to the template, as a list action would.
 */
abstract class AbstractEventListContext extends AbstractComponentContext
{
    abstract protected function getPageArgument(): string;

    public function beforeRendering(): void
    {
        $request = $this->getRequest();
        $argument = $this->getPageArgument();
        $currentPage = $request instanceof RequestInterface && $request->hasArgument($argument)
            ? (int)$request->getArgument($argument)
            : 1;

        $this->getRenderingContext()->getVariableProvider()->add('paginator', EventArchive::paginate($currentPage));
    }
}
