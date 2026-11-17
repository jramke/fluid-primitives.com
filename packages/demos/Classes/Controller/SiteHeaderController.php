<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Service\CurrentUser;
use FluidPrimitives\Demos\Service\DemoPages;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * The header of a website: the demo pages as navigation and a menu for the logged-in user. Uncached, it shows
 * who is logged in.
 */
final class SiteHeaderController extends ActionController
{
    public function __construct(
        private readonly DemoPages $demoPages,
    ) {}

    public function showAction(): ResponseInterface
    {
        $overview = $this->demoPages->overview($this->request) ?? [];
        $overviewSlug = Typed::string($overview['slug'] ?? null);

        $this->view->assignMultiple([
            'demos' => $this->demoPages->demos(Typed::int($overview['uid'] ?? null)),
            'overviewSlug' => $overviewSlug,
            'loginSlug' => $overviewSlug . '/login',
            'accountSlug' => $overviewSlug . '/account',
            'pagePath' => $this->pagePath(),
            'user' => CurrentUser::fromRequest($this->request),
        ]);

        return $this->htmlResponse();
    }

    private function pagePath(): string
    {
        // @mago-expect analysis:mixed-assignment
        $pageInformation = $this->request->getAttribute('frontend.page.information');

        return $pageInformation instanceof PageInformation
            ? Typed::string($pageInformation->getPageRecord()['slug'] ?? null)
            : '';
    }
}
