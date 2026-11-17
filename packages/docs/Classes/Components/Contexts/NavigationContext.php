<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Services\VersionsProvider;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * The links, the search and the version select of the site header, so a page outside the docs plugin (the demos)
 * renders the same header without passing them.
 */
class NavigationContext extends AbstractComponentContext
{
    public const array MAIN_LINKS = [
        ['label' => 'The Pitch', 'href' => '/the-pitch'],
        ['label' => 'Documentation', 'href' => '/docs'],
        ['label' => 'Demos', 'href' => '/demos'],
        ['label' => 'GitHub', 'href' => '/github', 'external' => true],
    ];

    public function __construct(
        private readonly VersionsProvider $versionsProvider,
    ) {}

    /**
     * @return list<array{label: string, href: string, external?: true}>
     */
    public function getMainLinks(): array
    {
        return self::MAIN_LINKS;
    }

    /**
     * The search index of the command menu, an action of the docs plugin on the root page. Built with the router
     * of the site, so a page without the docs plugin gets the same URL.
     */
    public function getSearchUrl(): string
    {
        // @mago-expect analysis:mixed-assignment
        $site = $this->getRequest()->getAttribute('site');
        if (!$site instanceof Site) {
            return '';
        }

        return (string)$site->getRouter()->generateUri($site->getRootPageId(), [
            'tx_docs_docs' => ['action' => 'index', 'controller' => 'SearchIndex'],
        ]);
    }

    /**
     * @return list<array{label: string, href: string, current?: true}>
     */
    public function getVersions(): array
    {
        return $this->versionsProvider->getVersions();
    }
}
