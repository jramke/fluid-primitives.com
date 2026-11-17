<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Service\DemoPages;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The header of a website, with the demo pages as navigation.
 */
final class SiteHeaderController extends ActionController
{
    public function __construct(
        private readonly DemoPages $demoPages,
    ) {}

    public function showAction(): ResponseInterface
    {
        $overview = $this->demoPages->overview($this->request) ?? [];

        $this->view->assignMultiple([
            'demos' => $this->demoPages->demos(Typed::int($overview['uid'] ?? null)),
            'overviewSlug' => Typed::string($overview['slug'] ?? null),
            'pagePath' => $this->demoPages->currentSlug($this->request),
        ]);

        return $this->htmlResponse();
    }
}
