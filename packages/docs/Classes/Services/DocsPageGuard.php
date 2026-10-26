<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Routing\Aspect\ValidatedPathMapper;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;

/**
 * What a request has to be before `DocsController::showAction()` renders a page.
 *
 * Extbase merges the body of a POST over the arguments of the route, so a request to `/docs/introduction` can
 * carry another `path` than the one the route aspect validated. The page cache identifier knows neither the
 * method nor the body, so such a response would also be cached for the URL of the other page.
 */
final readonly class DocsPageGuard
{
    public function __construct(
        private ValidatedPathMapper $pathMapper,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    /**
     * @return string|null The path of a page that exists, or null when there is none.
     * @throws PropagateResponseException for a request that is neither GET nor HEAD
     */
    public function resolvePath(string $path, ServerRequestInterface $request): ?string
    {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'], strict: true)) {
            throw new PropagateResponseException(
                $this->responseFactory->createResponse(405)->withHeader('Allow', 'GET, HEAD'),
                405,
            );
        }

        return $this->pathMapper->resolve(rtrim($path, characters: '/'));
    }
}
