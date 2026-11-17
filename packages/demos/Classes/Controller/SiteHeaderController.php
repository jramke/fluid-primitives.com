<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The header of an imaginary website, its links are in the template.
 */
final class SiteHeaderController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}
