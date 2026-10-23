<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Middleware;

use FluidPrimitives\Docs\Services\LlmsTxtBuilder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * Serves /llms.txt (see https://llmstxt.org) directly, short-circuiting before routing - same shape as
 * RegistryMiddleware, since this isn't a docs page and has no nav.yaml-backed Markdown file behind it.
 *
 * Every link points to the base of the site. The `Host` and `X-Forwarded-Host` headers of the request decide
 * nothing here: the response is cached by URL, and an index that links to another host would be an instruction
 * for every agent that reads it.
 */
final readonly class LlmsTxtMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LlmsTxtBuilder $llmsTxtBuilder,
        private StreamFactoryInterface $streamFactory,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/llms.txt') {
            return $handler->handle($request);
        }

        // Narrowed right below. Not NullSite: a host that matches no site has no base URL to link to.
        // @mago-expect analysis:mixed-assignment
        $site = $request->getAttribute('site');
        if (!$site instanceof Site || !in_array($request->getMethod(), ['GET', 'HEAD'], strict: true)) {
            return $this->responseFactory->createResponse(404)->withHeader('Content-Type', 'text/plain; charset=utf-8');
        }

        $base = $site->getBase();
        $port = $base->getPort();
        $baseUrl = $base->getScheme() . '://' . $base->getHost() . ($port === null ? '' : ':' . $port);

        $body = $this->llmsTxtBuilder->buildForSite($baseUrl);

        return $this->responseFactory
            ->createResponse(200)
            ->withBody($this->streamFactory->createStream($body))
            ->withHeader('Content-Type', 'text/plain; charset=utf-8')
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('X-Content-Type-Options', 'nosniff');
    }
}
