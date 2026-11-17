<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Service;

use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * The demo pages: the subpages of the first level page of the rootline, `/demos`, that show in menus.
 */
final readonly class DemoPages
{
    public function __construct(
        private PageRepository $pageRepository,
    ) {}

    /**
     * @return array<array-key, mixed>|null The overview page of the request, `/demos`
     */
    public function overview(ServerRequestInterface $request): ?array
    {
        // @mago-expect analysis:mixed-assignment
        $pageInformation = $request->getAttribute('frontend.page.information');

        return $pageInformation instanceof PageInformation
            ? Typed::arrayOrNull($pageInformation->getLocalRootLine()[1] ?? null)
            : null;
    }

    public function currentSlug(ServerRequestInterface $request): string
    {
        // @mago-expect analysis:mixed-assignment
        $pageInformation = $request->getAttribute('frontend.page.information');

        return $pageInformation instanceof PageInformation
            ? Typed::string($pageInformation->getPageRecord()['slug'] ?? null)
            : '';
    }

    /**
     * @return array<array-key, mixed>|null The demo of the request, the second level page of the rootline
     */
    public function demo(ServerRequestInterface $request): ?array
    {
        // @mago-expect analysis:mixed-assignment
        $pageInformation = $request->getAttribute('frontend.page.information');

        return $pageInformation instanceof PageInformation
            ? Typed::arrayOrNull($pageInformation->getLocalRootLine()[2] ?? null)
            : null;
    }

    /**
     * @return list<array{slug: string, title: string, abstract: string, preview: false}>
     */
    public function demos(int $overviewUid): array
    {
        $demos = [];
        // @mago-expect analysis:mixed-assignment
        foreach ($this->pageRepository->getMenu($overviewUid) as $menuItem) {
            $page = Typed::array($menuItem);
            if (
                Typed::bool($page['nav_hide'] ?? null) ||
                Typed::int($page['doktype'] ?? null) !== PageRepository::DOKTYPE_DEFAULT
            ) {
                continue;
            }
            $demos[] = [
                'slug' => Typed::string($page['slug'] ?? null),
                'title' => Typed::string($page['title'] ?? null),
                'abstract' => Typed::string($page['abstract'] ?? null),
                'preview' => false,
            ];
        }

        return $demos;
    }
}
