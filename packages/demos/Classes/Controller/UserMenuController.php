<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Service\CurrentUser;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The menu of the logged-in user in the site header, or a link to the login. Uncached, it shows who is logged in.
 */
final class UserMenuController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        $this->view->assign('user', CurrentUser::fromRequest($this->request));

        return $this->htmlResponse();
    }
}
