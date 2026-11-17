<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Service\CurrentUser;
use FluidPrimitives\Demos\Service\DemoPages;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The menu of the logged-in user: their initials, a link to the account settings and the logout. Uncached, it
 * shows who is logged in.
 */
final class UserMenuController extends ActionController
{
    public function __construct(
        private readonly DemoPages $demoPages,
    ) {}

    public function showAction(): ResponseInterface
    {
        $overview = $this->demoPages->overview($this->request) ?? [];
        $accountSlug = Typed::string($overview['slug'] ?? null) . '/login/account';

        $this->view->assignMultiple([
            'user' => CurrentUser::fromRequest($this->request),
            'accountSlug' => $accountSlug,
            'isAccountPage' => $this->demoPages->currentSlug($this->request) === $accountSlug,
        ]);

        return $this->htmlResponse();
    }
}
