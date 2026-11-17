<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\ViewHelpers;

use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * The URL of every page of a paginator, for the `pageUrls` of `ui:pagination.root`, when the plugin builds its
 * own paginator, like ke_search or the news search. Each URL is the current one with the page number in the query
 * argument `argument`, and a cHash when the arguments need one.
 *
 * ```html
 * <ui:pagination.root
 *     paginator="{pagination.paginator}"
 *     pageUrls="{demos:pageUrls(paginator: pagination.paginator, argument: 'tx_kesearch_pi1[page]')}"
 * />
 * ```
 */
final class PageUrlsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('paginator', PaginatorInterface::class, 'The paginator of the list.', true);
        $this->registerArgument('argument', 'string', 'The query argument that holds the page number.', true);
    }

    /**
     * @return array<int, string>
     */
    public function render(): array
    {
        // @mago-expect analysis:mixed-assignment(3)
        $request = $this->renderingContext?->getAttribute(ServerRequestInterface::class);
        $site = $request instanceof ServerRequestInterface ? $request->getAttribute('site') : null;
        $routing = $request instanceof ServerRequestInterface ? $request->getAttribute('routing') : null;
        $paginator = $this->arguments['paginator'];
        if (
            !$request instanceof ServerRequestInterface ||
            !$site instanceof Site ||
            !$routing instanceof PageArguments ||
            !$paginator instanceof PaginatorInterface
        ) {
            return [];
        }

        $query = Typed::array($request->getQueryParams());
        unset($query['cHash']);
        $argument = Typed::string($this->arguments['argument']);

        $pageUrls = [];
        foreach (range(1, max(1, $paginator->getNumberOfPages())) as $page) {
            $pageArgument = [];
            parse_str($argument . '=' . $page, $pageArgument);
            $pageUrls[$page] = (string)$site->getRouter()->generateUri($routing->getPageId(), array_replace_recursive(
                $query,
                $pageArgument,
            ));
        }

        return $pageUrls;
    }
}
