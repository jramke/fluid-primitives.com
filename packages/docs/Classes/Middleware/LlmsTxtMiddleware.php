<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Middleware;

use FluidPrimitives\Docs\Services\LlmsTxtBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Serves /llms.txt (see https://llmstxt.org) directly, short-circuiting before routing - same shape as
 * RegistryMiddleware, since this isn't a docs page and has no nav.yaml-backed Markdown file behind it.
 */
final readonly class LlmsTxtMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LlmsTxtBuilder $llmsTxtBuilder,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/llms.txt') {
            return $handler->handle($request);
        }

        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();

        $body = $this->llmsTxtBuilder->build($baseDir, $baseDir . 'nav.yaml', $baseUrl);

        return new Response($this->streamFactory->createStream($body), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
