<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\PaginationExamples;

use FluidPrimitives\Docs\Services\EventArchive;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Pagination\UrlAwarePaginator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Plays the controller of the link examples: it reads the page argument and assigns a `paginator`
 * with the URLs of its pages to the template, as a list action would.
 */
abstract class AbstractEventListContext extends AbstractComponentContext
{
    abstract protected function getPageArgument(): string;

    abstract protected function getSection(): string;

    public function beforeRendering(): void
    {
        $request = $this->getRequest();
        if (!$request instanceof RequestInterface) {
            return;
        }

        $argument = $this->getPageArgument();
        $currentPage = $request->hasArgument($argument) ? (int)$request->getArgument($argument) : 1;
        // The docs page keeps its path in the query string, a list action would use uriFor()
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class)->setRequest($request);
        $paginator = new UrlAwarePaginator(EventArchive::paginate($currentPage), fn(int $page): string => $uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->setArgumentsToBeExcludedFromQueryString(['tx_docs_docs[' . $argument . ']'])
            ->setArguments($page > 1 ? ['tx_docs_docs' => [$argument => $page]] : [])
            ->setSection($this->getSection())
            ->build());

        $this->getRenderingContext()->getVariableProvider()->add('paginator', $paginator);
    }
}
